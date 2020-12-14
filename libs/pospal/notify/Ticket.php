<?php

namespace libs\pospal\notify;

use api\models\Goods;
use api\models\Order;
use api\models\OrderGoods;
use api\models\User;
use Yii;
use yii\base\ErrorException;

/**
 * @author Jony
 *
 * @inheritdoc
 */
class Ticket extends Base
{

    /**
     * @param $params
     *
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     * @throws \yii\web\HttpException
     */
    public function actionNew($params)
    {
        if(!isset($params['sn'])){
            $this->out('Error ticket!');
        }

        $ticketId = $params['sn'];

        $result = $this->api->ticket->get([
            'sn' => $ticketId,
        ]);

        if(!$result->isSuccess()){
            $this->out('Query ticket fail!', true);
        }

        $data = $result->getData();

        if($data['ticketType'] != 'SELL'){
            $this->out('Not sell ticket!');
        }

        $orderId = $data['sn'];
        $amount = $data['totalAmount'];
        $userId = $data['customerUid'];

        if(!$userId){
            return;
        }

        $balance = 0.00;
        if(isset($params['customerBalanceUsedLogs'])){
            foreach($params['customerBalanceUsedLogs'] as $log){
                $balance += $log['usedMoney'];
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            if($userId){
                $user = User::findByOpenId($userId);

                if($user){
                    $debt = $user->amount - $balance;

                    if($debt < 0){
                        $user->amount = 0;
                        $user->debt += ($debt * -1);
                    }else{
                        $user->amount = $debt;
                    }

                    if(!$user->save()){
                        throw new ErrorException('余额扣除失败！' . print_r($user->getErrors(), true));
                    }
                }
            }

            $order = new Order();
            $order->order_id = Order::genId();
            $order->pospal_id = $orderId;
            $order->is_offline = Order::IS_OFFLINE_YES;
            $order->payment_id = '';
            $order->user_id = $user ? $user->user_id : 0;
            $order->store_id = $this->store->store_id;
            $order->freight_id = 0;
            $order->amount = $amount;
            $order->cost = 0.00;
            $order->saving = 0.00;
            $order->fee = 0.00;
            $order->consignee = $user ? $user->username : '散客';
            $order->area_id = 0;
            $order->address = $this->store->name;
            $order->phone = $user ? $user->mobile : '0';
            $order->memo = '';
            $order->delivery_type = 'offline';
            $order->delivery_number = (string) $data['cashierUid'];
            $order->status = Order::STATUS_DONE;

            $saving = 0;
            foreach($data['items'] as $item){
                $goodsModel = Goods::findByPospalId($item['productUid']);

                if(!$goodsModel){
                    $goodsModel = Goods::syncByPospalId($item['productUid'], $this->store);
                }else{
                    $goodsModel->updateStock($item['quantity']);
                }

                $goodsModel->updateSell($item['quantity']);

                if($goodsModel->info->stock == 0 && $goodsModel->status == Goods::STATUS_ACTIVE){
                    $goodsModel->status = Goods::STATUS_OFFLINE;
                    $goodsModel->save();
                }

                if($item['discount']){
                    $discount = $item['discount'] / 100;
                }else if($item['isCustomerDiscount']){
                    $discount = $item['customerDiscount'] / 100;
                }

                $price = $item['sellPrice'];
                if($item['isCustomerDiscount']){
                    $price = $item['customerPrice'];
                }

                $orderGoods = new OrderGoods();
                $orderGoods->order_id = $order->order_id;
                $orderGoods->user_id = $user ? $user->user_id : 0;
                $orderGoods->goods_id = $goodsModel->goods_id;
                $orderGoods->service_id = '';
                $orderGoods->name = $goodsModel->name;
                $orderGoods->preview = $goodsModel->preview ? $goodsModel->preview : 'empty';
                $orderGoods->original_price = $goodsModel->original_price;
                $orderGoods->member_price = $goodsModel->member_price;
                $orderGoods->quantity = $item['quantity'];
                $orderGoods->price = $price * $discount;
                $orderGoods->amount = $item['totalAmount'];
                $orderGoods->cost = $item['totalAmount'] - $item['totalProfit'];
                $orderGoods->attrs = '';
                $orderGoods->mode = '';
                $orderGoods->payment_status = OrderGoods::PAYMENT_DONE;
                $orderGoods->delivery_status = OrderGoods::DELIVERY_DONE;
                $orderGoods->status = OrderGoods::STATUS_DONE;
                if(!$orderGoods->save()){
                    throw new ErrorException('订单商品保存失败！' . print_r($orderGoods->getErrors(), true));
                }

                $order->cost += $orderGoods->cost;
                $saving += ($orderGoods->original_price - $orderGoods->price) * $item['quantity'];
            }

            $order->saving = $saving;

            if($order->saving < 0){
                $order->saving = 0;
            }

            if(!$order->save()){
                throw new ErrorException('订单保存失败！' . print_r($order->getErrors(), true));
            }

            $order->setOfflineReward();

            $transaction->commit();

            $this->out('success');
        }catch(ErrorException $exception){
            $transaction->rollBack();

            Yii::error($exception->getMessage() . "\n Data => " . print_r($data, true), 'notify');

            $this->out($exception->getMessage());
        }
    }
}

?>
