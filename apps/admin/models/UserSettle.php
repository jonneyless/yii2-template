<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "{{%user_settle}}".
 *
 * {@inheritdoc}
 *
 * @property \admin\models\User $user
 */
class UserSettle extends \common\models\UserSettle
{

    /**
     * @return User|\yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
