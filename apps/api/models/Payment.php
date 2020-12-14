<?php

namespace api\models;

use ijony\helpers\Url;
use ijony\helpers\Utils;
use libs\payment\alipay\AopClient;
use libs\payment\alipay\request\AlipayTradeAppPayRequest;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * 支付单数据模型
 *
 * {@inheritdoc}
 *
 * @property User $user
 */
class Payment extends \common\models\Payment
{

    public function afterFind()
    {
        parent::afterFind();

        if($this->isTimeout() && $this->isNew()){
            $this->status = self::STATUS_EXPIRE;
            $this->save();
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function isTimeout()
    {
        return $this->created_at + 1800 < time();
    }

    public function isExpire()
    {
        return $this->status == self::STATUS_EXPIRE;
    }

    public function isDoing()
    {
        return $this->status == self::STATUS_DOING;
    }

    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    public function isNew()
    {
        return $this->status == self::STATUS_NEW;
    }

    public function isCancel()
    {
        return $this->status == self::STATUS_CANCEL;
    }

    public function getPayMethon()
    {
        $return = $this->pay_type;

        if($return == 'balance'){
            $return = 'CustomerBalance';
        }

        return ucfirst($return);
    }

    public function done($payType)
    {
        if($this->isDone()){
            return;
        }

        $orderIds = Json::decode($this->orders);

        if($this->type == 'order'){
            /* @var $orders \api\models\Order[] */
            $orders = Order::find()->where(['order_id' => $orderIds])->all();
        }else{
            /* @var $orders \api\models\UserRenew[] */
            $orders = UserRenew::find()->where(['renew_id' => $orderIds])->all();
        }

        $this->pay_type = $payType;
        $this->status = self::STATUS_DONE;

        foreach($orders as $order){
            $order->paid($this);
        }

        if(!$this->save()){
            throw new Exception('支付单更新失败！');
        }
    }

    public function getAlipayCode()
    {
        $content = [];
        $content['subject'] = "来就省订单支付";
        $content['out_trade_no'] = $this->payment_id;
        $content['timeout_express'] = '30m';
        $content['total_amount'] = floatval($this->amount);
        if(YII_ENV === 'dev'){
            $content['total_amount'] = 0.01;
        }
        $content['product_code'] = "QUICK_MSECURITY_PAY";

        $content = Json::encode($content);

        $client = new AopClient();
        $client->appId = Yii::$app->params['payment']['alipay']['app_id'];
        $client->rsaPrivateKey = Yii::$app->params['payment']['alipay']['private_key'];
        $client->alipayrsaPublicKey = Yii::$app->params['payment']['alipay']['public_key'];
//        if(YII_ENV !== 'prod'){
//            $client->appId = Yii::$app->params['payment']['alipay_sandbox']['app_id'];
//            $client->rsaPrivateKey = Yii::$app->params['payment']['alipay_sandbox']['private_key'];
//            $client->alipayrsaPublicKey = Yii::$app->params['payment']['alipay_sandbox']['public_key'];
//            $client->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
//        }
        $client->format = 'json';
        $client->charset = 'UTF-8';
        $client->signType = 'RSA2';

        $request = new AlipayTradeAppPayRequest();
        $request->setBizContent($content);
        $request->setNotifyUrl(Url::getFull('/alipay-notify.html'));

        return $client->sdkExecute($request);
    }

    public function getWechatCode($type, $openid)
    {
        /* @var $payment \EasyWeChat\Payment\Payment */
        $payment = Yii::$app->wechat->payment;

        $params = [
            'body' => "来就省订单支付",
            'out_trade_no' => $this->payment_id,
            'total_fee' => $this->amount * 100,
            'trade_type' => \EasyWeChat\Payment\Order::APP,
            'notify_url' => Url::getFull('/wechat-notify.html'),
        ];

        if ($type == 'jssdk') {
            $params['trade_type'] = \EasyWeChat\Payment\Order::JSAPI;

            /* @var $miniProgram \EasyWeChat\MiniProgram\MiniProgram */
            $miniProgram = Yii::$app->wechat->mini_program;
            $session = $miniProgram->sns->getSessionKey($openid);
            $params['openid'] = $session['openid'];
        }

        if(YII_ENV === 'dev'){
            $params['total_fee'] = 1;
        }

        $order = new \EasyWeChat\Payment\Order($params);
        $result = $payment->prepare($order);

        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS'){
            Yii::error($result, 'pay');
            throw new BadRequestHttpException($result['result_code']);
        }

        if ($type == 'app') {
            return $payment->configForAppPayment($result['prepay_id']);
        } else {
            return $payment->configForJSSDKPayment($result['prepay_id']);
        }
    }

    public function payByBalance()
    {
        if($this->user->amount < $this->amount){
            throw new BadRequestHttpException('账户余额不足！');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $this->user->amount = $this->user->amount - $this->amount;
            if(!$this->user->save()){
                throw new BadRequestHttpException('账户金额扣除失败！');
            }

            $this->done('balance');

            $transaction->commit();

            $this->user->syncBalance(-1 * $this->amount);

        }catch(Exception $e){
            $transaction->rollBack();

            Yii::error($e->getMessage(), 'pay');

            throw new Exception('支付失败！', $e->getCode(), $e);
        }
    }

    public function showPayType()
    {
        switch ($this->pay_type) {
            case 'alipay' :

                return '支付宝';

                break;
            case 'wxpay' :

                return '微信支付';

                break;
            case 'balance' :

                return '余额支付';

                break;
            default :

                return null;

                break;
        }
    }

    /**
     * 生成唯一订单号
     *
     * @return string
     */
    public static function genId()
    {
        $id = date("YmdHis", time()) . Utils::getRand(6, true);

        if(self::find()->where(['payment_id' => $id])->exists()){
            $id = self::genId();
        }

        return $id;
    }
}
