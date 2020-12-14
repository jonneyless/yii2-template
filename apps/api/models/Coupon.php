<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%coupon}}".
 *
 * {@inheritdoc}
 */
class Coupon extends \common\models\Coupon
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function setUsed()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        if($user->expire_at < time()){
            $user->expire_at = strtotime("+1 day", strtotime(date("Y-m-d", time())));
        }

        $user->expire_at += ($this->month * 30 + $this->day) * 3600 * 24;

        if(!$user->save()){
            throw new ErrorException('兑换失败！');
        }

        $this->user_id = $user->user_id;

        if(!$this->save()){
            $user->syncUpdate();
            throw new ErrorException('兑换失败！');
        }
    }
}
