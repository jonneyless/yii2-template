<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * 购物车数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\Goods $goods
 */
class Cart extends namespace\base\Cart
{

    const QUICK_NO = 0;
    const QUICK_YES = 1;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 购物车商品信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }
}
