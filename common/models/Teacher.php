<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%teacher}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 关联用户 ID
 * @property string $store_id 所属店铺 ID
 * @property string $name 名称
 * @property string $title 头衔
 * @property string $intro 介绍
 * @property string $avatar 头像
 * @property int $created_at 注册时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Teacher extends namespace\base\Teacher
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用
}
