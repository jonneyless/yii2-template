<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_withdraw}}".
 *
 * @property string $withdraw_id 提现 ID
 * @property string $user_id 用户 ID
 * @property string $amount 金额
 * @property string $type 提现方式
 * @property string $account 提现账号
 * @property int $created_at 申请时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class UserWithdraw extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_withdraw}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['amount'], 'number'],
            [['account'], 'string'],
            [['type'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'withdraw_id' => '提现 ID',
            'user_id' => '用户 ID',
            'amount' => '金额',
            'type' => '提现方式',
            'account' => '提现账号',
            'created_at' => '申请时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
