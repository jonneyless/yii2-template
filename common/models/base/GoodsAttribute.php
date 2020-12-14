<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods_attribute}}".
 *
 * @property string $goods_id 商品 ID
 * @property string $name 属性名
 * @property string $value 属性值
 */
class GoodsAttribute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_attribute}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'name', 'value'], 'required'],
            [['goods_id'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['value'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品 ID',
            'name' => '属性名',
            'value' => '属性值',
        ];
    }
}
