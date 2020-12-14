<?php

namespace api\models;

use Yii;

/**
 * 店铺商品分类数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\Category $parent
 */
class StoreCategory extends \common\models\StoreCategory
{

    public function buildViewData()
    {
        return [
            'store_category_id' => $this->category_id,
            'name' => $this->name,
        ];
    }
}
