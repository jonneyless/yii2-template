<?php

namespace common\models;

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
class Performance extends namespace\base\Performance
{
}
