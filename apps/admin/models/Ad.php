<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%ad}}".
 *
 * {@inheritdoc}
 */
class Ad extends \common\models\Ad
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['sort', 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'status' => '启用',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if($this->image && substr($this->image, 0, 6) == BUFFER_FOLDER){
            $oldImg = $this->image;
            $newImg = Image::copyImg($this->image);

            if($newImg){
                File::delFile($oldImg, true);
            }

            $this->image = $newImg;
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        File::delFile($this->preview, true);
    }

    public function getImage()
    {
        return Image::getImg($this->image);
    }

    /**
     * 获取类型表述
     *
     * @return mixed|string
     */
    public function getType()
    {
        $datas = $this->getTypeSelectData();

        return isset($datas[$this->type]) ? $datas[$this->type] : '';
    }

    /**
     * 获取类型标签
     *
     * @return mixed|string
     */
    public function getTypeLabel()
    {
        if($this->type == self::TYPE_FOCUS){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getType(), $class);
    }

    /**
     * 获取完整类型数据
     *
     * @return array
     */
    public function getTypeSelectData()
    {
        return [
            self::TYPE_FOCUS => '聚焦广告',
            self::TYPE_GUIDE => '首页导航',
        ];
    }

    /**
     * 获取模式表述
     *
     * @return mixed|string
     */
    public function getMode()
    {
        $datas = $this->getModeSelectData();

        return isset($datas[$this->mode]) ? $datas[$this->mode] : '';
    }

    /**
     * 获取模式标签
     *
     * @return mixed|string
     */
    public function getModeLabel()
    {
        if($this->mode == self::MODE_CATEGORY){
            $class = 'label-primary';
        }else if($this->mode == self::MODE_GOODS){
            $class = 'label-success';
        }else if($this->mode == self::MODE_STORE){
            $class = 'label-info';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getMode(), $class);
    }

    /**
     * 获取完整模式数据
     *
     * @return array
     */
    public function getModeSelectData()
    {
        return [
            self::MODE_CATEGORY => '分类',
            self::MODE_GOODS => '商品',
            self::MODE_STORE => '店铺',
            self::MODE_URL => '网址',
        ];
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
        if($this->status == self::STATUS_ACTIVE){
            $class = 'label-primary';
        }else{
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
