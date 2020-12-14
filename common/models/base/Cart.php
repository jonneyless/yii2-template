<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property string $cart_id 购物车 ID
 * @property string $user_id 用户 ID
 * @property string $goods_id 商品 ID
 * @property int $quantity 数量
 * @property string $attrs 商品属性
 * @property string $mode 商品规格
 * @property int $quick 快速购买
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cart_id'], 'required'],
            [['user_id', 'goods_id', 'quantity', 'quick', 'created_at', 'updated_at'], 'integer'],
            [['attrs'], 'string'],
            [['cart_id'], 'string', 'max' => 20],
            [['mode'], 'string', 'max' => 255],
            [['cart_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cart_id' => '购物车 ID',
            'user_id' => '用户 ID',
            'goods_id' => '商品 ID',
            'quantity' => '数量',
            'attrs' => '商品属性',
            'mode' => '商品规格',
            'quick' => '快速购买',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
        ];
    }
}
