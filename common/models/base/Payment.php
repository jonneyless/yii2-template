<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%payment}}".
 *
 * @property string $payment_id 支付单号
 * @property string $type 订单类型
 * @property string $pay_type 支付类型
 * @property string $user_id 用户 ID
 * @property string $amount 总金额
 * @property string $orders 订单资料
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id', 'user_id'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['amount'], 'number'],
            [['orders'], 'string'],
            [['payment_id', 'type'], 'string', 'max' => 20],
            [['pay_type'], 'string', 'max' => 60],
            [['payment_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_id' => '支付单号',
            'type' => '订单类型',
            'pay_type' => '支付类型',
            'user_id' => '用户 ID',
            'amount' => '总金额',
            'orders' => '订单资料',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
