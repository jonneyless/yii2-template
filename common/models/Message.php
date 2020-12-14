<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%message}}".
 *
 * {@inheritdoc}
 */
class Message extends namespace\base\Message
{

    const TYPE_SYSTEM = 'system';           //系统
    const TYPE_DELIVERY = 'delivery';       //物流
    const TYPE_PROMOTION = 'promotion';     //促销

    const IS_ALL_NO = 0;
    const IS_ALL_YES = 1;

    const STATUS_HIDDEN = 0;
    const STATUS_SHOW = 9;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['is_all', 'default', 'value' => self::IS_ALL_NO],
            ['is_all', 'in', 'range' => [self::IS_ALL_NO, self::IS_ALL_YES]],
            ['status', 'default', 'value' => self::STATUS_HIDDEN],
            ['status', 'in', 'range' => [self::STATUS_HIDDEN, self::STATUS_SHOW]],
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
