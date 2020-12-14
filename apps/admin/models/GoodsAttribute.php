<?php

namespace admin\models;

use Yii;

/**
 * 商品属性数据模型
 *
 * {@inheritdoc}
 */
class GoodsAttribute extends \common\models\GoodsAttribute
{

    public function beforeSave($insert)
    {
        $this->name = trim($this->name);
        $this->value = trim($this->value);

        return parent::beforeSave($insert);
    }
}
