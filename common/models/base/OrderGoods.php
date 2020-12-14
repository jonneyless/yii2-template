<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%order_goods}}".
 *
 * @property string $order_id 订单号
 * @property string $user_id 用户 ID
 * @property string $goods_id 商品 ID
 * @property string $service_id 售后编号
 * @property string $name 商品名称
 * @property string $preview 商品主图
 * @property string $original_price 原价
 * @property string $member_price 会员价
 * @property int $quantity 数量
 * @property string $price 单价
 * @property string $amount 总金额
 * @property string $cost 成本
 * @property string $attrs 商品属性
 * @property string $mode 商品规格
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $payment_status 支付状态
 * @property int $delivery_status 发货状态
 * @property int $status 状态
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_goods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'goods_id', 'name', 'preview'], 'required'],
            [
                [
                    'user_id',
                    'goods_id',
                    'quantity',
                    'created_at',
                    'updated_at',
                    'payment_status',
                    'delivery_status',
                    'status',
                ],
                'integer',
            ],
            [['original_price', 'member_price', 'price', 'amount', 'cost'], 'number'],
            [['attrs'], 'string'],
            [['order_id', 'service_id'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['mode'], 'string', 'max' => 255],
            [['order_id', 'goods_id', 'mode'], 'unique', 'targetAttribute' => ['order_id', 'goods_id', 'mode']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单号',
            'user_id' => '用户 ID',
            'goods_id' => '商品 ID',
            'service_id' => '售后编号',
            'name' => '商品名称',
            'preview' => '商品主图',
            'original_price' => '原价',
            'member_price' => '会员价',
            'quantity' => '数量',
            'price' => '单价',
            'amount' => '总金额',
            'cost' => '成本',
            'attrs' => '商品属性',
            'mode' => '商品规格',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'payment_status' => '支付状态',
            'delivery_status' => '发货状态',
            'status' => '状态',
        ];
    }
}
