<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%product_gallery}}".
 *
 * @property string $product_id 商品 ID
 * @property string $image 组图
 * @property string $description 说明
 * @property int $sort 排序
 */
class ProductGallery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product_gallery}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'image'], 'required'],
            [['product_id', 'sort'], 'integer'],
            [['image'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 255],
            [['product_id', 'image'], 'unique', 'targetAttribute' => ['product_id', 'image']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => '商品 ID',
            'image' => '组图',
            'description' => '说明',
            'sort' => '排序',
        ];
    }
}
