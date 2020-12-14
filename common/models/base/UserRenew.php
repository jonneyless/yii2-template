<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_renew}}".
 *
 * @property string $renew_id 续费 ID
 * @property string $payment_id 支付单号
 * @property int $user_id 用户 ID
 * @property int $month 时长
 * @property string $amount 费用
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class UserRenew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_renew}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['renew_id', 'user_id'], 'required'],
            [['user_id', 'month', 'created_at', 'updated_at', 'status'], 'integer'],
            [['amount'], 'number'],
            [['renew_id', 'payment_id'], 'string', 'max' => 20],
            [['renew_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'renew_id' => '续费 ID',
            'payment_id' => '支付单号',
            'user_id' => '用户 ID',
            'month' => '时长',
            'amount' => '费用',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
