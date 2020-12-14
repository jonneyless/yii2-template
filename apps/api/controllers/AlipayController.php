<?php
namespace api\controllers;

use api\models\Payment;
use libs\payment\alipay\AopClient;
use Yii;
use yii\base\Exception;
use yii\web\Controller;

/**
 * Alipay controller
 */
class AlipayController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * 支付宝通知回调
     */
    public function actionNotify()
    {
        $params = Yii::$app->request->getBodyParams();

        if(!isset($params['out_trade_no'])){
            Yii::error($params, 'pay');
        }

        try{
            $payment_id = $params['out_trade_no'];

            $client = new AopClient();
            $client->alipayrsaPublicKey = Yii::$app->params['payment']['alipay']['public_key'];

            if(!$client->rsaCheckV1($params, NULL, "RSA2")){
                throw new Exception('验签失败！');
            }

            $payment = Payment::findOne($payment_id);

            if(!$payment){
                throw new Exception('支付单不存在！');
            }

            if($payment->isCancel()){
                throw new Exception('支付已取消！');
            }

            $transaction = Yii::$app->db->beginTransaction();

            try{
                $payment->done('alipay');

                $transaction->commit();

                echo 'success';
            }catch(Exception $e){
                $transaction->rollBack();

                Yii::error($e->getMessage(), 'pay');

                throw new Exception('支付失败！', $e->getCode(), $e);
            }
        }catch(Exception $e){
            Yii::error($e->getMessage(), 'pay');

            echo 'failure';
        }
    }
}
