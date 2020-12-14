<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_income}}".
 *
 * {@inheritdoc}
 */
class UserIncome extends namespace\base\UserIncome
{

    const TYPE_DIRECT = 'direct';
    const TYPE_INDIRECT = 'indirect';
    const TYPE_COMPANY = 'company';
    const TYPE_CITY = 'city';

    const RELATION_TYPE_USER = 'User';
    const RELATION_TYPE_STORE = 'Store';

    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
//        return [
//            TimestampBehavior::className(),
//        ];
//    }
}
