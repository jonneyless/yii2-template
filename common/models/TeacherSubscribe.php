<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%teacher_subscribe}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 用户 ID
 * @property string $name 姓名
 * @property string $phone 电话
 * @property string $subscribe_at 预约时间
 * @property int $created_at 预约时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class TeacherSubscribe extends namespace\base\TeacherSubscribe
{

    const STATUS_CANCEL = 0;    // 取消
    const STATUS_APPLY = 1;  // 申请
    const STATUS_DONE = 9;    // 完成
}
