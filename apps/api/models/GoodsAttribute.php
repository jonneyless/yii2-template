<?php

namespace api\models;

use Yii;

/**
 * 商品属性数据模型
 *
 * {@inheritdoc}
 */
class GoodsAttribute extends \common\models\GoodsAttribute
{

    public function fields()
    {
        return [
            'name',
            'value',
        ];
    }
}
