<?php

namespace api\models;

use ijony\helpers\Image;
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
 *
 * @property Store $store
 */
class Teacher extends \common\models\Teacher
{

    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }

    public function isSubscribed()
    {
        return TeacherSubscribe::find()->where(['teacher_id' => $this->teacher_id, 'user_id' => Yii::$app->user->id, 'status' => TeacherSubscribe::STATUS_APPLY])->exists();
    }

    public function buildListData($lng, $lat)
    {
        return [
            'teacher_id' => (int) $this->teacher_id,
            'name' => $this->name,
            'store_id' => (int) $this->store_id,
            'store_name' => $this->store->name,
            'title' => $this->title,
            'intro' => $this->intro,
            'avatar' => Image::getImg($this->avatar, 375, 230, 'default.jpg'),
            'address' => $this->store->address,
            'longitude' => $this->store->longitude,
            'latitude' => $this->store->latitude,
            'distance' => $this->store->getDistance($lng, $lat),
            'status' => $this->isSubscribed(),
        ];
    }

    public function buildViewData()
    {
        return [
            'teacher_id' => (int) $this->teacher_id,
            'name' => $this->name,
            'store_id' => (int) $this->store_id,
            'store_name' => $this->store->name,
            'title' => $this->title,
            'intro' => $this->intro,
            'avatar' => Image::getImg($this->avatar, 375, 230, 'default.jpg'),
            'address' => $this->store->address,
            'longitude' => $this->store->longitude,
            'latitude' => $this->store->latitude,
            'status' => $this->isSubscribed(),
        ];
    }
}
