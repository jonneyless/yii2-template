<?php

namespace console\controllers;

use admin\models\Goods;
use common\models\Store;
use Yii;
use yii\console\Controller;

class StoreController extends Controller
{

    public function actionSync($id)
    {
        $store = Store::findOne($id);

        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal->setStore($store->pospal_app_id, $store->pospal_app_key);
        $result = $pospal->user->queryMemberInfo();

        foreach($result->getData() as $datum){
            if($datum['name'] == '普通会员'){
                $store->pospal_normal_member = (string) $datum['uid'];
            }else if($datum['name'] == 'VIP 会员'){
                $store->pospal_vip_member = (string) $datum['uid'];
            }
        }

        $store->save();
    }

    public function actionGoods($id)
    {
        $goodses = Goods::find()->where(['store_id' => $id, 'status' => Goods::STATUS_ACTIVE])->andWhere(['<>', 'pospal_id', ''])->all();
        foreach($goodses as $goods){
            $goods->sync();
        }
    }
}