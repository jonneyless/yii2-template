<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%teacher_auth}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 用户 ID
 * @property string $code 验证码
 * @property int $created_at 预约时间
 * @property int $updated_at 更新时间
 */
class TeacherAuth extends \common\models\TeacherAuth
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
