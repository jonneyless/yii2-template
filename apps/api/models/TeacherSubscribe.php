<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;
use yii\behaviors\TimestampBehavior;

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
 *
 * @property Teacher $teacher
 */
class TeacherSubscribe extends \common\models\TeacherSubscribe
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['teacher_id' => 'teacher_id']);
    }

    public function buildListData()
    {
        return [
            'teacher_id' => (int) $this->teacher_id,
            'name' => $this->teacher->name,
            'store_id' => (int) $this->teacher->store_id,
            'store_name' => $this->teacher->store->name,
            'title' => $this->teacher->title,
            'intro' => $this->teacher->intro,
            'avatar' => Image::getImg($this->teacher->avatar, 375, 230, 'default.jpg'),
            'address' => $this->teacher->store->address,
            'status' => $this->status,
        ];
    }
}
