<?php

namespace console\controllers;

use api\models\Payment;
use Yii;
use yii\console\Controller;

class PaymentController extends Controller
{

    public function actionPaid($id)
    {
        $payment = Payment::findOne($id);

        if($payment && $payment->isDoing()){
            $transaction = Yii::$app->db->beginTransaction();

            try{
                $payment->done();

                $transaction->commit();

                echo "success\n";
            }catch(Exception $e){
                $transaction->rollBack();

                echo "failure\n";
            }
        }else{
            echo "failure\n";
        }
    }
}