<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%teacher}}".
 *
 * @property string $teacher_id 老师 ID
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

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $datas = Image::recoverImg($this->intro);

        $this->intro = $datas['content'];

        if ($this->avatar && substr($this->avatar, 0, 6) == BUFFER_FOLDER) {
            $oldImg = $this->avatar;
            $newImg = Image::copyImg($this->avatar);

            if ($newImg) {
                File::delFile($oldImg, true);
            }

            $this->avatar = $newImg;
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['avatar']) && $changedAttributes['avatar']) {
            File::delFile($changedAttributes['avatar'], true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        File::delFile($this->avatar, true);
    }

    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }

    /**
     * 店铺分类下拉表单数据
     * @return array
     */
    public function getStoreSelectData()
    {
        return Store::find()->select('name')->where(['store_id' => $this->store_id, 'status' => Store::STATUS_ACTIVE])->indexBy('store_id')->column();
    }

    /**
     * 获取主图
     *
     * @return mixed
     */
    public function getAvatar()
    {
        return Image::getImg($this->avatar);
    }

    /**
     * 获取状态表述
     *
     * @return mixed|string
     */
    public function getStatus()
    {
        $datas = $this->getStatusSelectData();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    /**
     * 获取状态标签
     *
     * @return mixed|string
     */
    public function getStatusLabel()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            $class = 'label-primary';
        } else {
            $class = 'label-danger';
        }

        return Utils::label($this->getStatus(), $class);
    }

    /**
     * 获取完整状态数据
     *
     * @return array
     */
    public function getStatusSelectData()
    {
        return [
            self::STATUS_UNACTIVE => '禁用',
            self::STATUS_ACTIVE => '启用',
        ];
    }
}
