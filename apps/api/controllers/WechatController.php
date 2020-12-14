<?php
namespace api\controllers;

use api\models\Payment;
use EasyWeChat\Core\Exceptions\FaultException;
use libs\payment\alipay\AopClient;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\Controller;

/**
 * Wechat controller
 */
class WechatController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * 微信通知回调
     */
    public function actionNotify()
    {
        /* @var $payment \EasyWeChat\Payment\Payment */
        $payment = Yii::$app->wechat->payment;

        try{
            $response = $payment->handleNotify(function($notify, $successful){
                $payment_id = $notify->get('out_trade_no');

                $payment = Payment::findOne($payment_id);

                if(!$payment){
                    return false;
                }

                if($payment->isCancel()){
                    return false;
                }

                if($successful){
                    $transaction = Yii::$app->db->beginTransaction();

                    try{
                        $payment->done('wxpay');

                        $transaction->commit();
                    }catch(Exception $e){
                        $transaction->rollBack();

                        return false;
                    }
                }

                return true;
            });

            $response->send();
        }catch(FaultException $e){
            Yii::error(Yii::$app->request->getRawBody(), 'pay');

            throw new ErrorException($e->getMessage());
        }
    }
}
