<?php

namespace common\models;

use libs\Utils;
use Yii;

/**
 * This is the model class for table "{{%goods_gallery}}".
 *
 * @property string $goods_id
 * @property string $image
 * @property integer $sort
 */
class GoodsGallery extends namespace\base\GoodsGallery
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_gallery}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'image'], 'required'],
            [['goods_id', 'sort'], 'integer'],
            [['image'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'image' => '图片地址',
            'sort' => '排序',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->image) {
            $this->image = Utils::coverBufferImage($this->image);
        }

        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        Utils::delFile($this->image, true);
    }

    public function getImage($width = 0, $height = 0)
    {
        return Utils::getImg($this->image, $width, $height);
    }
}
