<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Cart;
use api\models\Order;
use api\models\OrderGoods;
use api\models\Payment;
use api\models\Store;
use api\models\StoreFreight;
use api\models\UserAddress;
use Yii;
use yii\base\ErrorException;
use api\filters\QueryParamAuth;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class OrderController extends ApiController
{

    public $modelClass = 'api\models\Order';

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

        unset($actions['create']);

        return $actions;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionCreate()
    {
        $cart_ids = Yii::$app->request->post('cart_ids');
        $quick = Yii::$app->request->post('quick', Cart::QUICK_NO);
        $offline = Yii::$app->request->post('offline', Order::IS_OFFLINE_NO);
        $address_id = Yii::$app->request->post('address_id');
        $freight_ids = Yii::$app->request->post('freight_ids');
        $memos = Yii::$app->request->post('memos');

        if(!$cart_ids){
            throw new BadRequestHttpException('购物车数据不全！');
        }

        if(!$freight_ids){
            throw new BadRequestHttpException('快递尚未选择！');
        }

        $cart_ids = Json::decode($cart_ids);
        $freight_ids = Json::decode($freight_ids);
        $memos = Json::decode($memos);

        $items = Cart::find()->where(['cart_id' => $cart_ids, 'user_id' => Yii::$app->user->id, 'quick' => $quick])->all();
        /* @var $address \api\models\UserAddress */
        $address = UserAddress::find()->where(['address_id' => $address_id, 'user_id' => Yii::$app->user->id])->one();

        if(!$items){
            throw new BadRequestHttpException('没有需要支付的商品！');
        }

        if(!$address){
            throw new BadRequestHttpException('收货地址不存在！');
        }

        $return = [];
        $stores = [];
        $storeTotal = [];

        $transaction = Yii::$app->db->beginTransaction();

        try{
            foreach($items as $item){
                /* @var $item Cart */
                if(!$item->goods){
                    continue;
                }

                if(!$item->mode && $item->goods->mode){
                    throw new ErrorException('请选择' . $item->goods->name . '的' . $item->goods->mode['name'] . '！');
                }else if($item->mode && !$item->goods->checkMode($item->mode)){
                    throw new ErrorException('请重新选择' . $item->goods->name . '的' . $item->goods->mode['name'] . '！');
                }

                if(!isset($freight_ids[$item->goods->store_id])){
                    throw new ErrorException($item->goods->name . '的快递尚未选择！');
                }

                if(!$item->goods->updateStock($item->quantity)){
                    throw new ErrorException($item->goods->name . '库存不足！');
                }

                $price = $item->goods->getPrice();
                $cost_price = $item->goods->cost_price;
                $preview = $item->goods->preview;

                if($item->mode){
                    foreach($item->goods->mode['values'] as $value){
                        if($item->mode == $value['value']){
                            $price = $value['price'];
                            $cost_price = $value['cost'];
                            $preview = $value['image'];
                        }
                    }
                }

                $saving = ($item->goods->original_price - $price) * $item->quantity;
                $cost = $cost_price * $item->quantity;

                if($saving < 0){
                    $saving = 0;
                }

                $stores[$item->goods->store_id][] = [
                    'goods_id'       => $item->goods_id,
                    'name'           => $item->goods->name,
                    'preview'        => $preview,
                    'price'          => $price,
                    'original_price' => $item->goods->original_price,
                    'member_price'   => $item->goods->member_price,
                    'quantity'       => $item->quantity,
                    'amount'         => $price * $item->quantity,
                    'saving'         => $saving,
                    'cost'           => $cost,
                    'attrs'          => $item->attrs,
                    'mode'           => $item->mode,
                ];
            }

            $order_id = [];
            $orders = [];
            $totalAmount = 0.00;

            foreach($stores as $store_id => $items){
                /* @var $freight \api\models\StoreFreight */
                $freight_id = 0;
                if($freight_ids[$store_id]){
                    $freight = StoreFreight::find()->where(['freight_id' => $freight_ids[$store_id]])->one();
                    if(!$freight){
                        throw new ErrorException('快递数据错误！');
                    }
                    $freight_id = $freight->freight_id;
                }

                $store = Store::findOne($store_id);
                if(!$store){
                    throw new ErrorException('店铺不存在！');
                }

                $order = new Order();
                $order->order_id = Order::genId();
                $order->user_id = Yii::$app->user->id;
                $order->store_id = $store_id;
                $order->freight_id = $freight_id;
                $order->amount = 0.00;
                $order->saving = 0.00;
                $order->cost = 0.00;
                $order->fee = 0.00;
                $order->is_offline = $store->is_offline;
                $order->consignee = $address->consignee;
                $order->area_id = $address->area_id;
                $order->address = $address->getFullAddress();
                $order->phone = $address->phone;
                $order->memo = isset($memos[$store_id]) ? $memos[$store_id] : '';
                $order->status = Order::STATUS_NEW;

                if(!$order->save()){
                    Yii::error($order->getErrors());
                    throw new ErrorException('订单生成失败！');
                }

                foreach($items as $item){
                    $orderGoods = new OrderGoods();
                    $orderGoods->order_id = $order->order_id;
                    $orderGoods->user_id = $order->user_id;
                    $orderGoods->goods_id = $item['goods_id'];
                    $orderGoods->name = $item['name'];
                    $orderGoods->preview = $item['preview'];
                    $orderGoods->original_price = $item['original_price'];
                    $orderGoods->member_price = $item['member_price'];
                    $orderGoods->quantity = $item['quantity'];
                    $orderGoods->price = $item['price'];
                    $orderGoods->amount = $item['amount'];
                    $orderGoods->cost = $item['cost'];
                    $orderGoods->attrs = $item['attrs'];
                    $orderGoods->mode = $item['mode'] ? $item['mode'] : '';
                    $orderGoods->payment_status = OrderGoods::PAYMENT_NO;
                    $orderGoods->delivery_status = OrderGoods::DELIVERY_NO;
                    $orderGoods->status = OrderGoods::STATUS_NEW;

                    // 更新订单金额
                    $order->amount += $orderGoods->amount;
                    $order->saving += $item['saving'];
                    $order->cost += $orderGoods->cost;

                    if(!$orderGoods->save()){
                        Yii::error($orderGoods->getErrors());
                        throw new ErrorException('订单商品录入失败！');
                    }
                }

                if($freight_id){
                    $order->fee = $freight->getFee($order->area_id, $order->amount);
                    $order->amount += $order->fee;
                }

                if(!$order->save()){
                    throw new ErrorException('订单数据更新失败！' . print_r($order->getErrors(), true));
                }

                $order_id[] = $order->order_id;
                $orders[] = $order;
                $totalAmount += $order->amount;
            }

            sort($order_id);

            $orders = array_map(function($data){
                return $data->buildListData();
            }, $orders);

            $payment = new Payment();
            $payment->payment_id = (string) Payment::genId();
            $payment->type = 'order';
            $payment->user_id = Yii::$app->user->id;
            $payment->amount = $totalAmount;
            $payment->orders = Json::encode($order_id);

            if(!$payment->save()){
                Yii::error($payment->getErrors());
                throw new ErrorException('支付单生成失败！');
            }

            Cart::deleteAll(['cart_id' => $cart_ids, 'user_id' => Yii::$app->user->id, 'quick' => $quick]);

            $transaction->commit();

            return [
                'payment_id' => $payment->payment_id,
                'amount' => sprintf("%.02f", $payment->amount),
                'balance' => sprintf("%.02f", Yii::$app->user->identity->amount),
                'orders' => $orders,
            ];
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionPay()
    {

        $order_id = Yii::$app->request->getBodyParam('order_id');
        $order_id = Json::decode($order_id);

        $orders = Order::find()->where(['order_id' => $order_id, 'user_id' => Yii::$app->user->id])->all();

        if(!$orders){
            throw new BadRequestHttpException('订单不存在！');
        }

        $totalAmount = 0.00;
        $orders = array_map(function($data) use (&$totalAmount){
            /* @var $data \api\models\Order */
            $totalAmount += (float) $data->amount;

            return $data->buildListData();
        }, $orders);

        sort($order_id);

        $order_ids = Json::encode($order_id);

        $payment = Payment::find()->where(['user_id' => Yii::$app->user->id, 'orders' => $order_ids])->one();

        if(!$payment || !$payment->isNew()){
            $payment = new Payment();
            $payment->payment_id = (string) Payment::genId();
            $payment->type = 'order';
            $payment->user_id = Yii::$app->user->id;
            $payment->orders = $order_ids;
            $payment->status = Payment::STATUS_NEW;
        }

        $payment->amount = $totalAmount;

        if(!$payment->save()){
            Yii::error($payment->getErrors());
            throw new BadRequestHttpException('支付单生成失败！');
        }

        return [
            'payment_id' => $payment->payment_id,
            'amount' => sprintf("%.02f", $payment->amount),
            'orders' => $orders,
        ];
    }
}