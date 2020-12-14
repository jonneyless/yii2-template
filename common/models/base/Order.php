<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $goods_id
 * @property string $group_id
 * @property string $price
 * @property string $quantity
 * @property string $amount
 * @property string $paid
 * @property string $consignee
 * @property string $area_id
 * @property string $address
 * @property string $phone
 * @property string $delivery_name
 * @property string $delivery_number
 * @property string $pay_card
 * @property integer $is_first
 * @property string $created_at
 * @property string $updated_at
 * @property integer $payment_status
 * @property integer $delivery_status
 * @property integer $status
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'goods_id', 'group_id', 'quantity'], 'required'],
            [['user_id', 'goods_id', 'group_id', 'quantity', 'area_id', 'is_first', 'created_at', 'updated_at', 'payment_status', 'delivery_status', 'status'], 'integer'],
            [['price', 'amount', 'paid'], 'number'],
            [['id'], 'string', 'max' => 16],
            [['delivery_number', 'pay_card'], 'string', 'max' => 60],
            [['consignee', 'address', 'phone'], 'string', 'max' => 255],
            [['delivery_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单 ID',
            'user_id' => '用户 ID',
            'goods_id' => '商品 ID',
            'group_id' => '拼单 ID',
            'price' => '单价',
            'quantity' => '数量',
            'amount' => '总金额',
            'paid' => '已付金额',
            'consignee' => '收货人',
            'area_id' => '地址区域',
            'address' => '收货地址',
            'phone' => '联系电话',
            'delivery_name' => '物流名称',
            'delivery_number' => '物流单号',
            'pay_card' => '支付卡号',
            'is_first' => '发起人订单',
            'created_at' => '下单时间',
            'updated_at' => '更新时间',
            'payment_status' => '支付状态',
            'delivery_status' => '物流状态',
            'status' => '状态',
        ];
    }
}
