<?php

namespace common\models;

use Yii;

/**
 * 商品附加数据模型
 *
 * {@inheritdoc}
 */
class GoodsInfo extends namespace\base\GoodsInfo
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id'], 'integer'],
            [['stock', 'sell'], 'number'],
            [['goods_id'], 'unique'],
        ];
    }

    public function beforeSave($insert)
    {
        $this->stock = (string) $this->stock;
        $this->sell = (string) $this->sell;

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->stock = (float) $this->stock;
        $this->sell = (float) $this->sell;
    }
}
