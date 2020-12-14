<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $order_id 订单号
 * @property string $pospal_id 银豹 ID
 * @property int $is_offline 线下订单
 * @property string $payment_id 支付单号
 * @property string $user_id 用户 ID
 * @property string $store_id 店铺 ID
 * @property string $freight_id 快递模板 ID
 * @property string $amount 总金额
 * @property string $cost 成本
 * @property string $saving 节省金额
 * @property string $fee 运费金额
 * @property string $consignee 收货人
 * @property string $area_id 地区 ID
 * @property string $address 详细地址
 * @property string $phone 联系电话
 * @property string $memo 买家备注
 * @property string $delivery_type 快递类型
 * @property string $delivery_number 快递单号
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'store_id', 'consignee', 'area_id', 'address', 'phone'], 'required'],
            [['is_offline', 'user_id', 'store_id', 'freight_id', 'area_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['amount', 'cost', 'saving', 'fee'], 'number'],
            [['order_id', 'payment_id'], 'string', 'max' => 20],
            [['consignee'], 'string', 'max' => 30],
            [['address', 'memo'], 'string', 'max' => 255],
            [['pospal_id', 'phone'], 'string', 'max' => 60],
            [['delivery_type', 'delivery_number'], 'string', 'max' => 64],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单号',
            'pospal_id' => '银豹 ID',
            'is_offline' => '线下订单',
            'payment_id' => '支付单号',
            'user_id' => '用户 ID',
            'store_id' => '店铺 ID',
            'freight_id' => '快递模板 ID',
            'amount' => '总金额',
            'cost' => '成本',
            'saving' => '节省金额',
            'fee' => '运费金额',
            'consignee' => '收货人',
            'area_id' => '地区 ID',
            'address' => '详细地址',
            'phone' => '联系电话',
            'memo' => '买家备注',
            'delivery_type' => '快递类型',
            'delivery_number' => '快递单号',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
