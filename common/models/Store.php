<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 店铺数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\StoreFreight[] $freight
 */
class Store extends namespace\base\Store
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用

    const IS_OFFLINE_NO = 0;
    const IS_OFFLINE_YES = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['is_offline', 'default', 'value' => self::IS_OFFLINE_NO],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    /**
     * 店铺物流模板
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFreight()
    {
        return $this->hasMany(StoreFreight::className(), ['store_id' => 'store_id']);
    }
}
