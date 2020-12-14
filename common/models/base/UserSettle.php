<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_settle}}".
 *
 * @property string $user_id 用户 ID
 * @property string $year 年份
 * @property string $month 月份
 * @property string $amount 金额
 * @property int $status 状态
 */
class UserSettle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_settle}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'year', 'month', 'amount'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['year'], 'string', 'max' => 4],
            [['month'], 'string', 'max' => 2],
            [['user_id', 'year', 'month'], 'unique', 'targetAttribute' => ['user_id', 'year', 'month']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户 ID',
            'year' => '年份',
            'month' => '月份',
            'amount' => '金额',
            'status' => '状态',
        ];
    }
}
