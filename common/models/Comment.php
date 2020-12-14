<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%comment}}".
 *
 * {@inheritdoc}
 */
class Comment extends namespace\base\Comment
{

    const STATUS_HIDDEN = 0;
    const STATUS_SHOW = 9;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_SHOW],
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
