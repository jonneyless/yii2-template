<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * 支付单数据模型
 *
 * {@inheritdoc}
 */
class Payment extends namespace\base\Payment
{

    const STATUS_CANCEL = 0;
    const STATUS_NEW = 1;
    const STATUS_EXPIRE = 2;
    const STATUS_DOING = 3;
    const STATUS_DONE = 9;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_EXPIRE, self::STATUS_DOING, self::STATUS_DONE]],
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
