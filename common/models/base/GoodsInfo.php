<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods_info}}".
 *
 * @property string $goods_id 商品 ID
 * @property int $stock 库存
 * @property int $sell 销量
 */
class GoodsInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id', 'stock', 'sell'], 'integer'],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'stock' => '库存',
            'sell' => '销量',
        ];
    }
}
