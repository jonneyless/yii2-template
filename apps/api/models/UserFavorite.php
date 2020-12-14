<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;

/**
 * 用户收藏数据模型
 *
 * {@inheritdoc}
 *
 * @property \api\models\Goods|\api\models\Store $item
 */
class UserFavorite extends \common\models\UserFavorite
{

    public function buildData()
    {
        if($this->type == 0){
            $item = [
                'user_id' => $this->user_id,
                'type' => $this->type,
                'relation_id' => $this->relation_id,
                'goods_id'          => (int)$this->item->goods_id,
                'category_id'       => (int)$this->item->category_id,
                'store_id'          => (int)$this->item->store_id,
                'store_category_id' => (int)$this->item->store_category_id,
                'name'              => $this->item->name,
                'store_name' => $this->item->store->name,
                'preview'           => Image::getImg($this->item->preview, 300, 300, 'default.jpg'),
                'number'            => $this->item->number,
                'original_price'    => $this->item->original_price,
                'member_price'      => $this->item->member_price,
                'saving'            => sprintf("%.02f", $this->item->original_price - $this->item->member_price),
                'content'           => '',
                'created_at'        => date("Y-m-d H:i:s", $this->item->created_at),
                'updated_at'        => date("Y-m-d H:i:s", $this->item->updated_at),
                'shelves_at'        => date("Y-m-d", $this->item->shelves_at),
                'goods_score'       => $this->item->goods_score,
                'store_score'       => $this->item->store_score,
                'delivery_score'    => $this->item->delivery_score,
            ];
        }else{
            $item =  [
                'user_id' => $this->user_id,
                'type' => $this->type,
                'relation_id' => $this->relation_id,
                'store_id'       => (int)$this->item->store_id,
                'name'           => $this->item->name,
                'preview'        => Image::getImg($this->item->preview, 300, 300, 'default.jpg'),
                'service_phone'  => $this->item->service_phone,
                'service_qq'     => $this->item->service_qq,
                'content'        => '',
                'goods_count'    => $this->item->getGoods()->count(),
                'favorite_count' => $this->item->getFavorite()->count(),
            ];
        }

        return $item;
    }

    public function getItem()
    {
        $className = Goods::className();
        $primaryKey = 'goods_id';

        if($this->type == 1){
            $className = Store::className();
            $primaryKey = 'store_id';
        }

        return $this->hasOne($className, [$primaryKey => 'relation_id']);
    }
}
