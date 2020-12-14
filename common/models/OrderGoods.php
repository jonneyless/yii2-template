<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * 订单商品数据模型
 *
 * {@inheritdoc}
 */
class OrderGoods extends namespace\base\OrderGoods
{

    const STATUS_CANCEL = 0;        // 已取消
    const STATUS_NEW = 1;           // 待支付
    const STATUS_PAID = 2;          // 已支付
    const STATUS_DELIVERY = 3;      // 已发货
    const STATUS_DONE = 8;          // 已完成
    const STATUS_COMMENT = 9;       // 已评价

    const PAYMENT_NO = 0;           // 待支付
    const PAYMENT_REFUND_DONE = 1;  // 已退款
    const PAYMENT_WAITING = 2;      // 支付中
    const PAYMENT_REFUND = 8;       // 待退款
    const PAYMENT_DONE = 9;         // 已支付

    const DELIVERY_NO = 0;          // 待发货
    const DELIVERY_REFUND_DONE = 1; // 已退货
    const DELIVERY_WAITING = 2;     // 已发货
    const DELIVERY_CHANGE_DONE = 3; // 待退货
    const DELIVERY_CHANGE = 7;      // 待退货
    const DELIVERY_REFUND = 8;      // 待退货
    const DELIVERY_DONE = 9;        // 已收货

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['payment_status', 'default', 'value' => self::PAYMENT_NO],
            ['payment_status', 'in', 'range' => [self::PAYMENT_NO, self::PAYMENT_REFUND_DONE, self::PAYMENT_WAITING, self::PAYMENT_REFUND, self::PAYMENT_DONE]],
            ['delivery_status', 'default', 'value' => self::DELIVERY_NO],
            ['delivery_status', 'in', 'range' => [self::DELIVERY_NO, self::DELIVERY_REFUND_DONE, self::DELIVERY_WAITING, self::DELIVERY_CHANGE_DONE, self::DELIVERY_CHANGE, self::DELIVERY_REFUND, self::DELIVERY_DONE]],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_PAID, self::STATUS_DELIVERY, self::STATUS_DONE, self::STATUS_COMMENT]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function beforeSave($insert)
    {
        $this->quantity = (string) $this->quantity;

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->quantity = (float) $this->quantity;
    }
}
