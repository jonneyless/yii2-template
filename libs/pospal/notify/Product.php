<?php

namespace libs\pospal\notify;

use admin\models\Goods;
use admin\models\GoodsInfo;
use admin\models\Store;
use Yii;

/**
 * @author Jony
 *
 * @inheritdoc
 */
class Product extends Base
{

    public function actionEdit(array $params)
    {
        $stores = [];

        foreach ($params as $param) {
            if (isset($param['barcode'])) {
                $result = $this->api->product->queryByBarcode([
                    'barcode' => $param['barcode'],
                ]);

                if(!$result->isSuccess()){
                    continue;
                }

                if(!isset($stores[$this->api->appId])){
                    $stores[$this->api->appId] = Store::find()->where(['pospal_app_id' => $this->api->appId])->one();
                }

                if($result->data === null && $stores[$this->api->appId]){
                    Goods::updateAll(['status' => Goods::STATUS_DELETE], ['bar_code' => $param['barcode'], 'store_id' => $stores[$this->api->appId]->store_id]);
                    continue;
                }

                $store_id = $stores[$this->api->appId] ? $stores[$this->api->appId]->store_id : 0;
                $goods = Goods::find()->where(['bar_code' => $param['barcode'], 'store_id' => $store_id])->one();
                if(!$goods){
                    $goods = new Goods();
                    $goods->store_id = $store_id;
                    $goods->bar_code = $param['barcode'];
                    $goods->status = Goods::STATUS_OFFLINE;
                }

                $goods->pospal_id = $result->getData('uid');
                $goods->name = $result->getData('name');
                $goods->cost_price = $result->getData('buyPrice');
                $goods->original_price = $result->getData('sellPrice');
                $goods->save(false);

                $goodsInfo = GoodsInfo::find()->where(['goods_id' => $goods->goods_id])->one();
                if(!$goodsInfo){
                    $goodsInfo = new GoodsInfo();
                    $goodsInfo->goods_id = $goods->goods_id;
                }

                $goodsInfo->stock = $result->getData('stock');
                $goodsInfo->save();
            }
        }
    }
}

?>
