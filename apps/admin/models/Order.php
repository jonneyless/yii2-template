<?php

namespace admin\models;

use ijony\helpers\Utils;
use Yii;

/**
 * 订单数据模型
 *
 * {@inheritdoc}
 */
class Order extends \common\models\Order
{

    public function getItems()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }

    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    public function isDelivery()
    {
        return $this->status == self::STATUS_DELIVERY;
    }

    public function delivery()
    {
        if($this->isPaid() && $this->delivery_type && $this->delivery_number){
            $this->status = self::STATUS_DELIVERY;

            OrderGoods::updateAll(['delivery_status' => OrderGoods::DELIVERY_WAITING, 'status' => OrderGoods::STATUS_DELIVERY], ['order_id' => $this->order_id]);

            return $this->save();
        }

        return false;
    }

    /**
     * 获取订单状态
     *
     * @return mixed|string
     */
    public function getStatus()
    {
        $datas = $this->getStatusSelectDatas();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    /**
     * 获取订单状态下拉表单数据
     *
     * @return array
     */
    public function getStatusSelectDatas()
    {
        return [
            self::STATUS_CANCEL => '已取消',
            self::STATUS_NEW => '待支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_REFUND => '待退款',
            self::STATUS_DELIVERY => '已发货',
            self::STATUS_DONE => '已完成',
        ];
    }

    /**
     * 获取订单状态标签
     *
     * @return string
     */
    public function getStatuslabel()
    {
        $return = [];

        switch($this->status){
            default:
            case self::STATUS_CANCEL:

                $return[] = Utils::label('已取消');

                break;

            case self::STATUS_NEW:

                $return[] = Utils::label('待支付', 'label-info');

                break;

            case self::STATUS_PAID:

                $return[] = Utils::label('已支付', 'label-danger');

                break;

            case self::STATUS_REFUND:

                $return[] = Utils::label('待退款', 'label-warning');

                break;

            case self::STATUS_DELIVERY:

                $return[] = Utils::label('已发货', 'label-success');

                break;

            case self::STATUS_DONE:

                $return[] = Utils::label('已收货', 'label-primary');

                break;
        }

        return join(" ", $return);
    }
}
