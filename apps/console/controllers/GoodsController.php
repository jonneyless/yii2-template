<?php

namespace console\controllers;

use common\models\GoodsInfo;
use common\models\Product;
use common\models\ProductGallery;
use common\models\Goods;
use ijony\helpers\Folder;
use Yii;
use yii\console\Controller;

class GoodsController extends Controller
{

    public function actionFix()
    {
        $goodses = Goods::find()->where(['store_id' => 29, 'status' => [Goods::STATUS_ACTIVE, Goods::STATUS_OFFLINE]])->all();
        /* @var Goods[] $goodses */
        foreach($goodses as $goods){
            if($goods->pospal_id){
                /* @var \libs\pospal\Pospal $pospal */
                $pospal = Yii::$app->pospal->setStore($goods->store->pospal_app_id, $goods->store->pospal_app_key);
                $result = $pospal->product->member([
                    'productUid' => $goods->pospal_id,
                    'customerPriceInfo' => [
                        [
                            'categoryUid' => $goods->store->pospal_normal_member,
                            'price' => $goods->original_price,
                            'salable' => 1,
                        ],
                        [
                            'categoryUid' => $goods->store->pospal_vip_member,
                            'price' => $goods->member_price,
                            'salable' => 1,
                        ]
                    ],
                ]);
            }
        }
    }

    public function actionProduct()
    {
        $products = Product::find()->all();
        foreach($products as $product){
            if(file_exists(Folder::getStatic($product->preview))){
                continue;
            }

            $goods = Goods::find()->where(['product_id' => $product->id])->one();
            if($goods){
                $product->preview = $goods->preview;
                $product->save();
            }
        }

        $galleries = ProductGallery::find()->all();
        foreach($galleries as $gallery){
            if(!file_exists(Folder::getStatic($gallery->image))){
                $gallery->delete();
            }
        }
    }
}