<?php

namespace console\controllers;

use common\models\Goods;
use common\models\GoodsGroup;
use common\models\GoodsVirtual;
use common\models\Group;
use Yii;
use common\models\Order;
use yii\console\Controller;

/**
 * 拼单更新
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class GroupController extends Controller
{

    /**
     * 订单状态更新。不要动
     */
    public function actionFix($id)
    {
        $group = Group::findOne($id);

        if (!$group->isOver()) {
            return;
        }

        if ($group->goods->is_virtual == 0) {
            return;
        }

        $count = intval(GoodsVirtual::find()->where(['goods_id' => $group->goods_id, 'group_id' => $group->id])->count());

        if ($count == $group->delivery) {
            return;
        }

        $diff = $group->delivery - $count;

        for ($count; $count < $group->delivery; $count++) {
            /* @var $virtual \common\models\GoodsVirtual */
            $virtual = GoodsVirtual::find()->where(['goods_id' => $group->goods_id, 'group_id' => 0])->one();
            if ($virtual) {
                $virtual->group_id = $group->id;
                $virtual->status = GoodsVirtual::STATUS_USED;
                $virtual->save();
            }
        }

        /* @var $goodsGroup \common\models\GoodsGroup */
        $goodsGroup = GoodsGroup::find()->where(['goods_id' => $group->goods_id, 'quantity' => $group->quantity])->one();
        $goodsGroup->stock = $goodsGroup->stock - $diff;
        $goodsGroup->save();

        $group->goods->stock = $group->goods->stock - $diff;
        $group->goods->sales = $group->goods->sales + $diff;
        $group->goods->save();

        if ($group->goods->one_delivery == 0) {
            /* @var $orders \common\models\Order[] */
            $orders = Order::find()->where(['group_id' => $group->id, 'status' => Order::STATUS_DONE])->limit($group->quantity)->orderBy(['created_at' => SORT_ASC])->all();

            foreach ($orders as $order) {
                $virtual = GoodsVirtual::find()->where(['goods_id' => $group->goods_id, 'group_id' => $group->id, 'order_id' => $order->id])->one();
                if (!$virtual) {
                    $virtual = GoodsVirtual::find()->where(['goods_id' => $group->goods_id, 'group_id' => $group->id, 'order_id' => ''])->one();
                }
                $virtual->order_id = $order->id;
                $virtual->status = GoodsVirtual::STATUS_USED;
                $virtual->save();
            }
        }
    }
}