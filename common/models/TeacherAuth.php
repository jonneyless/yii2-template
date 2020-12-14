<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%teacher_auth}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 用户 ID
 * @property string $code 验证码
 * @property int $created_at 预约时间
 * @property int $updated_at 更新时间
 */
class TeacherAuth extends namespace\base\TeacherAuth
{
}
