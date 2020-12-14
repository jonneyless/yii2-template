<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Cart;
use api\models\Goods;
use api\models\Store;
use ijony\helpers\Image;
use Yii;
use api\filters\QueryParamAuth;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class CartController extends ApiController
{

    public $modelClass = 'api\models\Cart';

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['update'], $actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        $store_id = Yii::$app->request->getQueryParam('store_id', 0);

        $return = [];
        $stores = [];
        /* @var $items \api\models\Cart[] */
        $items = Cart::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC])->all();

        foreach($items as $item){
            if(!$item->goods){
                $item->delete();
                continue;
            }

            $goods = $item->buildData();

            if(!$goods){
                continue;
            }

            if($store_id > 0){
                if($store_id != $item->goods->store_id){
                    continue;
                }
            }else{
                if($item->goods->store->is_offline == Store::IS_OFFLINE_YES){
                    continue;
                }
            }

            if(!isset($stores[$item->goods->store_id])){
                $stores[$item->goods->store_id] = [];
                $return[] = &$stores[$item->goods->store_id];
            }

            $stores[$item->goods->store_id]['store_id'] = (integer) $item->goods->store_id;
            $stores[$item->goods->store_id]['store_name'] = $item->goods->store->name;
            if(isset($stores[$item->goods->store_id]['saving'])){
                $stores[$item->goods->store_id]['saving'] = floatval($stores[$item->goods->store_id]['saving']) + ($item->goods->original_price - $item->goods->member_price) * $item->quantity;
            }else{
                $stores[$item->goods->store_id]['saving'] = ($item->goods->original_price - $item->goods->member_price) * $item->quantity;
            }
            $stores[$item->goods->store_id]['saving'] = sprintf('%.2f', $stores[$item->goods->store_id]['saving']);
            $stores[$item->goods->store_id]['goods'][] = $goods;
        }

        return $return;
    }

    public function actionAdd()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $quantity = Yii::$app->request->post('quantity');
        $attrs = Yii::$app->request->post('attrs', null);
        $mode = Yii::$app->request->post('mode', '');
        $quick = Yii::$app->request->post('quick', Cart::QUICK_NO);

        $goods = Goods::findOne($goods_id);

        if(!$goods){
            throw new BadRequestHttpException('商品不存在！');
        }

        if(!$mode && $goods->mode){
            throw new BadRequestHttpException('请选择商品'.$goods->mode['name'].'！');
        }else if($mode && !$goods->checkMode($mode)){
            throw new BadRequestHttpException('商品'.$goods->mode['name'].'选择错误！');
        }

        if($attrs !== null){
            $attrs = Json::decode($attrs);
            ksort($attrs);
            $attrs = Json::encode($attrs);
        }

        Cart::deleteAll(['user_id' => Yii::$app->user->id, 'quick' => Cart::QUICK_YES]);

        if($quick == Cart::QUICK_YES){
            $model = new Cart();
            $model->cart_id = Cart::genId();
            $model->user_id = Yii::$app->user->id;
            $model->goods_id = $goods_id;
            $model->attrs = $attrs;
            $model->mode = $mode;
            $model->quick = Cart::QUICK_YES;
        }else{
            $params = [
                'goods_id' => $goods_id,
                'user_id' => Yii::$app->user->id,
                'attrs' => $attrs,
                'mode' => $mode,
                'quick' => Cart::QUICK_NO
            ];
            $model = Cart::find()->where($params)->one();

            if(!$model){
                $model = new Cart();
                $model->setAttributes($params);
                $model->cart_id = Cart::genId();
            }
        }

        $model->quantity += $quantity;

        if($model->quantity > $goods->info->stock){
            throw new BadRequestHttpException('商品库存不足！');
        }

        if(!$model->save()){
            if($quick == Cart::QUICK_YES){
                throw new BadRequestHttpException('购买失败！');
            }else{
                throw new BadRequestHttpException('添加到购物车失败！');
            }
        }

        if($quick == Cart::QUICK_YES){
            return $this->runAction('checkout');
        }

        return [
            'message' => '成功添加到购物车。',
        ];
    }

    public function actionUpdate($id)
    {
        $quantity = Yii::$app->request->getBodyParam('quantity');

        $model = Cart::find()->where(['cart_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$model){
            throw new BadRequestHttpException('购物车数据错误！');
        }

        $model->quantity = $quantity;

        if($model->quantity > $model->goods->info->stock){
            throw new BadRequestHttpException('商品库存不足！');
        }

        if(!$model->save()){
            throw new BadRequestHttpException('更新购物车失败！');
        }

        return [
            'message' => '购物车更新成功。',
        ];
    }

    public function actionDelete($id)
    {
        Cart::deleteAll(['cart_id' => $id, 'user_id' => Yii::$app->user->id]);

        return [
            'message' => '购物车商品删除成功。',
        ];
    }

    public function actionCheckout()
    {
        $cart_ids = Yii::$app->request->post('cart_ids');
        $quick = Yii::$app->request->post('quick', Cart::QUICK_NO);
        $address_id = Yii::$app->request->post('address_id');

//        if(!Yii::$app->user->identity->checkExpire()){
//            throw new BadRequestHttpException('对不起，你还不是会员或者会员已到期，不能结算！');
//        }

        if($quick == Cart::QUICK_YES){
            $items = Cart::find()->where(['user_id' => Yii::$app->user->id, 'quick' => Cart::QUICK_YES])->all();
        }else{
            if($cart_ids){
                $cart_ids = Json::decode($cart_ids);
            }

            $items = Cart::find()->where(['cart_id' => $cart_ids, 'user_id' => Yii::$app->user->id, 'quick' => Cart::QUICK_NO])->all();
        }

        if(!$items){
            throw new BadRequestHttpException('没有可结算商品！');
        }

        $return = [];
        $stores = [];
        $storeFeeTotal = [];
        $storeFreeTotal = [];

        foreach($items as $index => $item){
            /* @var $item \api\models\Cart */
            if(!$item->goods){
                if(!$item->getIsNewRecord()){
                    $item->delete();
                }
                unset($items[$index]);
                continue;
            }

            $goods = $item->buildData();
            if(!$goods){
                if(!$item->getIsNewRecord()){
                    $item->delete();
                }
                unset($items[$index]);
                continue;
            }

            if(!isset($storeFeeTotal[$item->goods->store_id])){
                $storeFeeTotal[$item->goods->store_id] = 0;
            }

            if(!isset($storeFreeTotal[$item->goods->store_id])){
                $storeFreeTotal[$item->goods->store_id] = 0;
            }

            $price = Yii::$app->user->identity->checkExpire() ? $goods['member_price'] : $goods['original_price'];
            $amount = $price * $item->quantity;

            if($item->goods->free_express && $item->goods->free_express <= $item->quantity){
                $storeFreeTotal[$item->goods->store_id] += $amount;
            }else{
                $storeFeeTotal[$item->goods->store_id] += $amount;
            }

            if(!isset($stores[$item->goods->store_id])){
                $stores[$item->goods->store_id] = [];
                $return['stores'][] = &$stores[$item->goods->store_id];
            }

            $stores[$item->goods->store_id]['store_id'] = (integer) $item->goods->store_id;
            $stores[$item->goods->store_id]['store_name'] = $item->goods->store->name;
            $stores[$item->goods->store_id]['goods'][] = $goods;
        }

        foreach($items as $item){
            /* @var $item \api\models\Cart */
            $stores[$item->goods->store_id]['freight'] = $item->goods->store->getDeliveryFreight(Yii::$app->user->identity->getDeliveryProvince($address_id), $storeFeeTotal[$item->goods->store_id], $storeFreeTotal[$item->goods->store_id]);
        }

        $return['delivery'] = Yii::$app->user->identity->getDeliveryAddress($address_id);
        $return['notify'] = null;

        return $return;
    }
}