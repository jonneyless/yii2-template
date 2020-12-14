<?php

namespace console\controllers;

use api\models\Store;
use Yii;
use yii\console\Controller;

class PospalController extends Controller
{

    public function actionApi($store_id, $begin, $end)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        if($store_id){
            $store = Store::findOne($store_id);
            $pospal->setStore($store->pospal_app_id, $store->pospal_app_key);
        }

        print_r($pospal->base->apiDaily([
            'beginDate' => $begin,
            'endDate' => $end,
        ]));
    }

    public function actionGoods($barcode)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $result = $pospal->product->queryByBarcode(['barcode' => $barcode]);
        print_r($result);
    }

    public function actionGoodsById($id)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $result = $pospal->product->queryById(['productUid' => $id]);
        print_r($result);
    }

    public function actionNotify($store_id)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        if($store_id){
            $store = Store::findOne($store_id);
            $pospal->setStore($store->pospal_app_id, $store->pospal_app_key);
        }

        print_r($pospal->base->notify());
    }

    public function actionSetNotify($store_id)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        if($store_id){
            $store = Store::findOne($store_id);
            $pospal->setStore($store->pospal_app_id, $store->pospal_app_key);
        }

        print_r($pospal->base->setNotify(['pushUrl' => 'http://api.shop.beiyindi.cn/pospal-notify.html']));
    }
}