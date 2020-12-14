<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_message}}".
 *
 * {@inheritdoc}
 */
class UserMessage extends namespace\base\UserMessage
{

    const IS_READ_NO = 0;
    const IS_READ_YES = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['is_read', 'default', 'value' => self::IS_READ_NO],
            ['is_read', 'in', 'range' => [self::IS_READ_NO, self::IS_READ_YES]],
        ]);
    }
}
