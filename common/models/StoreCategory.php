<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 店铺商品分类数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\Category $parent
 */
class StoreCategory extends namespace\base\StoreCategory
{

    const STATUS_UNACTIVE = 0;    // 禁用
    const STATUS_ACTIVE = 9;      // 启用

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'parent_id']);
    }
}
