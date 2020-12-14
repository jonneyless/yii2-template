<?php

namespace admin\models;

use Yii;
use yii\helpers\Json;

/**
 * 订单商品数据模型
 *
 * {@inheritdoc}
 */
class OrderGoods extends \common\models\OrderGoods
{

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    public function showAttrs()
    {
        if(!$this->attrs){
            return '';
        }

        $attrs = Json::decode($this->attrs);

        foreach($attrs as $name => &$attr){
            $attr = sprintf('%s：%s', $name, $attr);
        }

        return join("<br>", $attrs);
    }
}
