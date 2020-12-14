<?php

namespace common\models;

use Yii;

/**
 * 产品分类数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\Category $parent
 */
class Category extends namespace\base\Category
{

    const STATUS_UNACTIVE = 0;    // 禁用
    const STATUS_ACTIVE = 9;      // 启用

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'parent_id']);
    }
}
