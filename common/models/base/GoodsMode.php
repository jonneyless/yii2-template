<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods_mode}}".
 *
 * @property string $goods_id 商品 ID
 * @property string $name 名称
 * @property string $value 货品
 * @property string $price 会员价
 * @property string $original_price 商超价
 * @property string $cost_price 成本价
 * @property int $stock 库存
 * @property string $image 图片
 */
class GoodsMode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_mode}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'name', 'value'], 'required'],
            [['goods_id', 'stock'], 'integer'],
            [['price', 'original_price', 'cost_price'], 'number'],
            [['name', 'value', 'image'], 'string', 'max' => 255],
            [['goods_id', 'value'], 'unique', 'targetAttribute' => ['goods_id', 'value']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'name' => '名称',
            'value' => '货品',
            'price' => '会员价',
            'original_price' => '商超价',
            'cost_price' => '成本价',
            'stock' => '库存',
            'image' => '图片',
        ];
    }
}
