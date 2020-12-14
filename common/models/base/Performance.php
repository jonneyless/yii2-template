<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%performance}}".
 *
 * @property string $user_id 用户 ID
 * @property string $company_id 公司 ID
 * @property string $agent_id 代理 ID
 * @property string $agent_company_id 代理公司 ID
 * @property string $city_id 城市代理 ID
 * @property string $order_id 订单号
 * @property string $amount 业绩
 * @property int $is_offline 线下业绩
 * @property int $year 年份
 * @property int $month 月份
 */
class Performance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%performance}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'company_id', 'agent_id', 'agent_company_id', 'order_id', 'amount', 'year', 'month'], 'required'],
            [['user_id', 'company_id', 'agent_id', 'agent_company_id', 'city_id', 'order_id', 'is_offline', 'year', 'month'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户 ID',
            'company_id' => '公司 ID',
            'agent_id' => '代理 ID',
            'agent_company_id' => '代理公司 ID',
            'city_id' => '城市代理 ID',
            'order_id' => '订单号',
            'amount' => '业绩',
            'is_offline' => '线下业绩',
            'year' => '年份',
            'month' => '月份',
        ];
    }
}
