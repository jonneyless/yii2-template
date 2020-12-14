<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * 订单数据模型
 *
 * {@inheritdoc}
 */
class Order extends namespace\base\Order
{

    const STATUS_CANCEL = 0;
    const STATUS_NEW = 1;
    const STATUS_PAID = 2;
    const STATUS_REFUND = 3;
    const STATUS_DELIVERY = 4;
    const STATUS_DONE = 9;

    const IS_OFFLINE_NO = 0;
    const IS_OFFLINE_YES = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['is_offline', 'default', 'value' => self::IS_OFFLINE_NO],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_PAID, self::STATUS_REFUND, self::STATUS_DELIVERY, self::STATUS_DONE]],
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
}
