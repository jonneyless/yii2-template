<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "{{%store_statement}}".
 *
 * @property int $store_id 店铺 ID
 * @property string $date 年月
 * @property string $offline 线下利润
 * @property string $online 线上利润
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class StoreStatement extends \common\models\StoreStatement
{
}
