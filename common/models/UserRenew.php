<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_renew}}".
 *
 * {@inheritdoc}
 */
class UserRenew extends namespace\base\UserRenew
{

    const STATUS_CANCEL = 0;
    const STATUS_NEW = 1;
    const STATUS_EXPIRE = 2;
    const STATUS_DONE = 9;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_EXPIRE, self::STATUS_DONE]],
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
