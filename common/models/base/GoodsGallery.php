<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods_gallery}}".
 *
 * @property string $goods_id
 * @property string $image
 * @property integer $sort
 */
class GoodsGallery extends \yii\db\ActiveRecord
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
}
