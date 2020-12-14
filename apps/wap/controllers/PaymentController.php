<?php

namespace wap\controllers;

use common\models\GoodsVirtual;
use common\models\Group;
use common\models\Order;
use common\models\Payment;
use libs\ccbpay\Wap;
use libs\CryptAES;
use libs\Utils;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Url;

/**
 * 支付回调
 *
 * @package wap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class PaymentController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * 1.2 接口的支付调用页
     *
     * // TODO 改用 2.1 接口后可以删除
     *
     * @return string
     */
    public function actionPostApp()
    {
        $this->layout = 'pay';

        $params['TXCODE'] = Yii::$app->request->get('TXCODE');
        $params['MERCHANTID'] = Yii::$app->request->get('MERCHANTID');
        $params['ORDERID'] = Yii::$app->request->get('ORDERID');
        $params['PAYMENT'] = Yii::$app->request->get('PAYMENT');
        $params['BRANCHID'] = Yii::$app->request->get('BRANCHID');
        $params['POSID'] = Yii::$app->request->get('POSID');
        $params['CURCODE'] = Yii::$app->request->get('CURCODE');
        $params['REMARK1'] = Yii::$app->request->get('REMARK1');
        $params['REMARK2'] = Yii::$app->request->get('REMARK2');
        $params['WAPVER'] = Yii::$app->request->get('WAPVER');
        $params['MAGIC'] = Yii::$app->request->get('MAGIC');

        $platform = Yii::$app->request->get('PLATFORM');

        return $this->render('post-app', ['params' => $params, 'platform' => $platform]);
    }

    /**
     * 支付回调跳转页
     *
     * @return string
     */
    public function actionReturn()
    {

        $orderId = '';
        $authKey = '';
        if (Yii::$app->request->getIsPost()) {
            $orderId = Yii::$app->request->post('ORDERID');
            $authKey = Yii::$app->request->post('CK');
            $params = [
                'MERCHANTID' => Yii::$app->request->post('MERCHANTID'),
                'POSID' => Yii::$app->request->post('POSID'),
                'BRANCHID' => Yii::$app->request->post('BRANCHID'),
                'ORDERID' => Yii::$app->request->post('ORDERID'),
                'PAYMENT' => Yii::$app->request->post('PAYMENT'),
                'REMARK1' => Yii::$app->request->post('REMARK1'),
                'REMARK2' => Yii::$app->request->post('REMARK2'),
                'BJOURNAL' => Yii::$app->request->post('BJOURNAL'),
                'DN' => Yii::$app->request->post('DN'),
                'SUCCESS' => Yii::$app->request->post('SUCCESS'),
                'SIGNBANK' => Yii::$app->request->post('SIGNBANK'),
            ];
        } else {
            $orderId = Yii::$app->request->get('ORDERID');
            $authKey = Yii::$app->request->get('CK');
            $params = [
                'MERCHANTID' => Yii::$app->request->get('MERCHANTID'),
                'POSID' => Yii::$app->request->get('POSID'),
                'BRANCHID' => Yii::$app->request->get('BRANCHID'),
                'ORDERID' => Yii::$app->request->get('ORDERID'),
                'PAYMENT' => Yii::$app->request->get('PAYMENT'),
                'REMARK1' => Yii::$app->request->get('REMARK1'),
                'REMARK2' => Yii::$app->request->get('REMARK2'),
                'BJOURNAL' => Yii::$app->request->get('BJOURNAL'),
                'DN' => Yii::$app->request->get('DN'),
                'SUCCESS' => Yii::$app->request->get('SUCCESS'),
                'SIGNBANK' => Yii::$app->request->get('SIGNBANK'),
            ];
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

//            $aes = new CryptAES();
//            $aes->require_pkcs5();
//
//            if($aes->encrypt(Utils::arrayToStr($params)) != $authKey){
//                throw new ErrorException('非法访问！');
//            }

            $payment = new Wap();
            $result = @$payment->respond();

            if (!$result) {
                throw new ErrorException('支付失败！');
            }

            /* @var $order \common\models\Order */
            $order = Order::findOne($orderId);

            if (!$order || !$order->goods) {
                throw new ErrorException('订单异常，请联系客服！');
            }

            if ($order->payment_status == Order::PAYMENT_DONE || $order->payment_status == Order::PAYMENT_REFUND) {
                return $this->redirect(['site/group', 'id' => $order->group_id]);
            }

            $order->paid = $order->amount;

            if ($order->group->status == Group::STATUS_OVER) {
                $order->status = Order::STATUS_CANCEL;
            }

            // 当是已失效得订单时，直接变更为待退款状态
            if ($order->status == Order::STATUS_CANCEL) {
                $order->payment_status = Order::PAYMENT_REFUND;
            } else {
                $order->payment_status = Order::PAYMENT_DONE;
                $order->status = Order::STATUS_PAID;

                if (!$order->save()) {
                    throw new ErrorException('订单更新错误，请联系客服！');
                }

                $order->group->joiner++;
                if ($order->group->status == Group::STATUS_UNACTIVE) {
                    $order->group->status = Group::STATUS_ACTIVE;
                }

                if (!$order->group->begin_at) {
                    $order->group->begin_at = time();
                }

                $order->group->save();
            }

            if (!$order->save()) {
                throw new ErrorException('订单更新错误，请联系客服！');
            }

            if (!$order->user->first_pay) {
                $order->user->first_pay = time();
                $order->user->save();
            }

            $transaction->commit();

            Yii::$app->log->targets[0]->logFile = Yii::$app->getRuntimePath() . '/logs/pay.log';
            Yii::error('支付成功！');

            return $this->message('支付成功！', Url::to(['site/group', 'id' => $order->group_id]));
        } catch (ErrorException $e) {

            $transaction->rollBack();

            Yii::$app->log->targets[0]->logFile = Yii::$app->getRuntimePath() . '/logs/pay.log';
            Yii::error($e->getMessage());

            return $this->message($e->getMessage(), Url::to(['site/index']));
        }
    }

    /**
     * 支付回调通知
     */
    public function actionNotify()
    {

        $orderId = '';
        $authKey = '';
        if (Yii::$app->request->getIsPost()) {
            $orderId = Yii::$app->request->post('ORDERID');
            $authKey = Yii::$app->request->post('CK');
            $params = [
                'MERCHANTID' => Yii::$app->request->post('MERCHANTID'),
                'POSID' => Yii::$app->request->post('POSID'),
                'BRANCHID' => Yii::$app->request->post('BRANCHID'),
                'ORDERID' => Yii::$app->request->post('ORDERID'),
                'PAYMENT' => Yii::$app->request->post('PAYMENT'),
                'REMARK1' => Yii::$app->request->post('REMARK1'),
                'REMARK2' => Yii::$app->request->post('REMARK2'),
                'BJOURNAL' => Yii::$app->request->post('BJOURNAL'),
                'DN' => Yii::$app->request->post('DN'),
                'SUCCESS' => Yii::$app->request->post('SUCCESS'),
                'SIGNBANK' => Yii::$app->request->post('SIGNBANK'),
            ];
        } else {
            $orderId = Yii::$app->request->get('ORDERID');
            $authKey = Yii::$app->request->get('CK');
            $params = [
                'MERCHANTID' => Yii::$app->request->get('MERCHANTID'),
                'POSID' => Yii::$app->request->get('POSID'),
                'BRANCHID' => Yii::$app->request->get('BRANCHID'),
                'ORDERID' => Yii::$app->request->get('ORDERID'),
                'PAYMENT' => Yii::$app->request->get('PAYMENT'),
                'REMARK1' => Yii::$app->request->get('REMARK1'),
                'REMARK2' => Yii::$app->request->get('REMARK2'),
                'BJOURNAL' => Yii::$app->request->get('BJOURNAL'),
                'DN' => Yii::$app->request->get('DN'),
                'SUCCESS' => Yii::$app->request->get('SUCCESS'),
                'SIGNBANK' => Yii::$app->request->get('SIGNBANK'),
            ];
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

//            $aes = new CryptAES();
//            $aes->require_pkcs5();
//
//            if($aes->encrypt(Utils::arrayToStr($params)) != $authKey){
//                throw new ErrorException('非法访问！');
//            }

            $payment = new Wap();
            $result = @$payment->respond();

            if (!$result) {
                throw new ErrorException('支付失败！');
            }

            /* @var $order \common\models\Order */
            $order = Order::findOne($orderId);

            if (!$order || !$order->goods) {
                throw new ErrorException('订单异常，请联系客服！');
            }

            if ($order->payment_status == Order::PAYMENT_DONE || $order->payment_status == Order::PAYMENT_REFUND) {
                if ($_SERVER && isset($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT'])) {
                    throw new ErrorException('订单已支付');
                } else {
                    throw new ErrorException('订单已支付');
                }
            }

            $order->paid = $order->amount;

            if ($order->group->status == Group::STATUS_OVER) {
                $order->status = Order::STATUS_CANCEL;
            }

            // 当是已失效得订单时，直接变更为待退款状态
            if ($order->status == Order::STATUS_CANCEL) {
                $order->payment_status = Order::PAYMENT_REFUND;

                if (!$order->save()) {
                    throw new ErrorException('订单更新错误，请联系客服！');
                }
            } else {
                $order->payment_status = Order::PAYMENT_DONE;
                $order->status = Order::STATUS_PAID;

                if (!$order->save()) {
                    throw new ErrorException('订单更新错误，请联系客服！');
                }

                $order->group->joiner++;
                if ($order->group->status == Group::STATUS_UNACTIVE) {
                    $order->group->status = Group::STATUS_ACTIVE;
                }

                if (!$order->group->begin_at) {
                    $order->group->begin_at = time();
                }

                $order->group->save();
            }

            if (!$order->user->first_pay) {
                $order->user->first_pay = time();
                $order->user->save();
            }

            $transaction->commit();

            if ($_SERVER && isset($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT'])) {
                throw new ErrorException('支付成功！');
            } else {
                throw new ErrorException('支付成功！');
            }
        } catch (ErrorException $e) {

            $transaction->rollBack();

            Yii::$app->log->targets[0]->logFile = Yii::$app->getRuntimePath() . '/logs/pay.log';
            Yii::error($e->getMessage());

            if ($_SERVER && isset($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT'])) {
                echo $e->getMessage();
            } else {
                echo $e->getMessage();
            }
        }
    }
}