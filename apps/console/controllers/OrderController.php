<?php

namespace console\controllers;

use common\models\Group;
use Yii;
use common\models\Order;
use yii\console\Controller;

/**
 * 订单更新
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class OrderController extends Controller
{

    /**
     * 订单状态更新。不要动
     */
    public function actionUpdate()
    {
        Order::find()->where(['status' => Order::STATUS_NEW])->all();
        Group::find()->where(['in', 'status', [Group::STATUS_ACTIVE, Group::STATUS_UNACTIVE]])->all();
    }

    public function actionFix()
    {
        $begin_time = strtotime("2017-04-24");
        $end_time = strtotime("2017-04-25");
        $file = Yii::$app->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'docs/bank.txt';
        $bank = file_get_contents($file);

        $orders = Order::find()->where(['between', 'created_at', $begin_time, $end_time])->andWhere(['status' => Order::STATUS_CANCEL, 'payment_status' => Order::PAYMENT_NO])->all();
        foreach ($orders as $order) {
            if (strpos($bank, $order->id) > 0) {
                $order->payment_status = Order::PAYMENT_REFUND;
                $order->paid = $order->amount;
                $order->save();
                echo $order->id . " 已处理\n";
            }
        }
    }
}