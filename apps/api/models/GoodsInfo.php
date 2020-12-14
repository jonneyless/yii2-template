<?php

namespace api\models;

use Yii;

/**
 * 商品附加数据模型
 *
 * {@inheritdoc}
 */
class GoodsInfo extends \common\models\GoodsInfo
{

    public function beforeSave($insert)
    {
        if($this->stock < 0){
            $this->stock = 0;
        }

        if($this->sell < 0){
            $this->sell = 0;
        }

        return parent::beforeSave($insert);
    }
}
