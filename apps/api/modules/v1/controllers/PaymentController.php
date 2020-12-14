<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Payment;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class PaymentController extends ApiController
{

    public $modelClass = 'api\models\Payment';

    public function actionAlipay()
    {
        $payment_id = Yii::$app->request->getBodyParam('payment_id');

        $payment = Payment::findOne($payment_id);

        if(!$payment){
            throw new BadRequestHttpException('支付单不存在！');
        }

        if($payment->isExpire()){
            throw new BadRequestHttpException('支付超时，请重新发起支付！');
        }

        $payment->status = Payment::STATUS_DOING;
        $payment->save();

        return [
            'code' => $payment->getAlipayCode(),
        ];
    }

    public function actionAlipayVerify()
    {
        $result = Yii::$app->request->getBodyParam('code');
        $code = Json::decode($result);

        if(isset($code['result'])){
            $code = $code['result'];
        }

        if(!is_array($code)){
            $code = Json::decode($code);
        }

        try{
            if(!isset($code['alipay_trade_app_pay_response']) || !isset($code['sign'])){
                throw new Exception('参数错误！');
            }

            $params = $code['alipay_trade_app_pay_response'];
            $payment_id = $params['out_trade_no'];
            $total_amount= $params['total_amount'];

//            $client = new AopClient();
//            $client->alipayrsaPublicKey = Yii::$app->params['payment']['alipay']['public_key'];
//
//            if(!$client->rsaCheckV1($code, NULL, 'RSA2')){
//                throw new Exception('验签失败！');
//            }

            $payment = Payment::findOne($payment_id);

            if(!$payment){
                throw new Exception('支付单不存在！');
            }

            if(YII_ENV_PROD && $payment->amount !== $total_amount){
                throw new Exception('数据错误！');
            }

            if(!$payment->isDone()){
                throw new Exception('支付失败！');
            }

//            $transaction = Yii::$app->db->beginTransaction();
//
//            try{
//                $payment->done();
//
//                $transaction->commit();
//
                return [
                    'message' => '支付成功！',
                ];
//            }catch(Exception $e){
//                $transaction->rollBack();
//
//                throw new Exception('支付失败！');
//            }
        }catch(Exception $e){
            Yii::error($result, 'pay');
            Yii::error($code, 'pay');

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionWechat()
    {
        $payment_id = Yii::$app->request->getBodyParam('payment_id');
        $type = Yii::$app->request->getBodyParam('type', 'app');
        $openid = Yii::$app->request->getBodyParam('openid');

        $payment = Payment::findOne($payment_id);

        if(!$payment){
            throw new BadRequestHttpException('支付单不存在！');
        }

        if($payment->isExpire()){
            throw new BadRequestHttpException('支付超时，请重新发起支付！');
        }

        $payment->status = Payment::STATUS_DOING;
        $payment->save();

        return [
            'code' => $payment->getWechatCode($type, $openid),
        ];
    }

    public function actionWechatVerify()
    {
        Yii::error(Yii::$app->request->getBodyParams(), 'pay');
        $payment_id = Yii::$app->request->getBodyParam('code');

        $payment = Payment::findOne($payment_id);

        if(!$payment){
            throw new BadRequestHttpException('支付单不存在！');
        }

        if(!$payment->isDone()){
            throw new Exception('支付失败！');
        }

        return [
            'message' => '支付成功！',
        ];
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionBalance()
    {
        $payment_id = Yii::$app->request->getBodyParam('payment_id');

        $payment = Payment::findOne($payment_id);

        if(!$payment){
            throw new BadRequestHttpException('支付单不存在！');
        }

        if($payment->isExpire()){
            throw new BadRequestHttpException('支付超时，请重新发起支付！');
        }

        $payment->payByBalance();

        return [
            'message' => '支付成功！',
            'balance' => $payment->user->amount,
        ];
    }
}