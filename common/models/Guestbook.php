<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%guestbook}}".
 *
 * {@inheritdoc}
 */
class Guestbook extends namespace\base\Guestbook
{

    const TYPE_MESSAGE = 'message';
    const TYPE_COMPLAINT = 'complaint';
    const TYPE_QUESTION = 'question';
    const TYPE_SERVICE = 'service';
    const TYPE_TOBUY = 'tobuy';

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
