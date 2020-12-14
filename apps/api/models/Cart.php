<?php

namespace api\models;

use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\helpers\Json;

/**
 * 购物车数据模型
 *
 * {@inheritdoc}
 *
 * @property \api\models\Goods $goods
 */
class Cart extends \common\models\Cart
{

    /**
     * 购物车商品信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    public function buildData()
    {
        $return  = [
            'cart_id'        => $this->cart_id,
            'goods_id'       => (int)$this->goods->goods_id,
            'name'           => $this->goods->name,
            'preview'        => Image::getImg($this->goods->preview, 300, 300, 'default'),
            'number'         => $this->goods->number,
            'cost_price'     => $this->goods->cost_price,
            'original_price' => $this->goods->original_price,
            'member_price'   => $this->goods->member_price,
            'attrs'          => $this->parseAttr(),
            'mode'          => $this->mode,
            'quantity'       => (int)$this->quantity,
            'stock'          => (int)$this->goods->info->stock,
        ];

        if($this->mode){
            $mode = GoodsMode::find()->where(['goods_id' => $this->goods_id, 'value' => $this->mode])->one();
            if(!$mode){
                $this->delete();
                return;
            }

            if($mode->price){
                $return['member_price'] = $mode->price;
            }

            if($mode->original_price){
                $return['original_price'] = $mode->original_price;
            }

            if($mode->cost_price){
                $return['cost_price'] = $mode->cost_price;
            }

            if($mode->stock){
                $return['stock'] = $mode->stock;
            }

            if($mode->image){
                $return['preview'] = Image::getImg($mode->image, 300, 300, 'default');
            }
        }

        return $return;
    }

    public function parseAttr()
    {
        if($this->attrs){
            $attrs = Json::decode($this->attrs);

            foreach($attrs as $name => &$value){
                $value = $name . "：" . $value;
            }

            sort($attrs);

            return $attrs;
        }

        return [];
    }

    public static function genId()
    {
        $id = sprintf(date("YmdHis"), Utils::getRand(6, true));

        if(self::find()->where(['cart_id' => $id])->exists()){
            $id = self::genId();
        }

        return $id;
    }
}
