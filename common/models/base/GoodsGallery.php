<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods_gallery}}".
 *
 * @property string $goods_id 商品 ID
 * @property string $image 组图
 * @property string $description 说明
 * @property int $sort 排序
 */
class GoodsGallery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_gallery}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'image'], 'required'],
            [['goods_id', 'sort'], 'integer'],
            [['image'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 255],
            [['goods_id', 'image'], 'unique', 'targetAttribute' => ['goods_id', 'image']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'image' => '组图',
            'description' => '说明',
            'sort' => '排序',
        ];
    }
}
