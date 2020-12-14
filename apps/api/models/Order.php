<?php

namespace api\models;

use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\Json;

/**
 * 订单数据模型
 *
 * {@inheritdoc}
 *
 * @property \api\models\Store $store
 * @property \api\models\User $user
 * @property \api\models\OrderGoods[] $goods
 * @property \api\models\StoreFreight $freight
 * @property \api\models\Payment $payment
 */
class Order extends \common\models\Order
{

    const API_STATUS_CANCEL = 'cancel';
    const API_STATUS_NEW = 'unpaid';
    const API_STATUS_PAID = 'paid';
    const API_STATUS_REFUND = 'refund';
    const API_STATUS_DELIVERY = 'delivery';
    const API_STATUS_DONE = 'done';

    private static $_status = [
        self::API_STATUS_CANCEL => self::STATUS_CANCEL,
        self::API_STATUS_NEW => self::STATUS_NEW,
        self::API_STATUS_PAID => self::STATUS_PAID,
        self::API_STATUS_REFUND => self::STATUS_REFUND,
        self::API_STATUS_DELIVERY => self::STATUS_DELIVERY,
        self::API_STATUS_DONE => self::STATUS_DONE,
    ];

    private static $_api_status = [
        self::STATUS_CANCEL => self::API_STATUS_CANCEL,
        self::STATUS_NEW => self::API_STATUS_NEW,
        self::STATUS_PAID => self::API_STATUS_PAID,
        self::STATUS_REFUND => self::API_STATUS_REFUND,
        self::STATUS_DELIVERY => self::API_STATUS_DELIVERY,
        self::STATUS_DONE => self::API_STATUS_DONE,
    ];

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }

    public function getGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }

    public function getFreight()
    {
        return $this->hasOne(StoreFreight::className(), ['freight_id' => 'freight_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }

    public function buildListData()
    {
        $goods = [];
        $saving = 0;

        if($this->goods){
            $goods = array_map(function($item) use (&$saving){
                /* @var $item \api\models\OrderGoods */
                $saving += (float) ($item->original_price - $item->price) * $item->quantity;

                return [
                    'goods_id' => (integer) $item->goods_id,
                    'goods_name' => $item->name,
                    'preview' => Image::getImg($item->preview, 300, 300, 'default.jpg'),
                    'original_price' => $item->original_price,
                    'member_price' => $item->member_price,
                    'quantity' => $item->quantity,
                    'attrs' => $item->parseAttr(),
                    'status' => $item->getApiStatus(),
                ];
            }, $this->goods);
        }

        return [
            'order_id' => $this->order_id,
            'store_id' => (integer) $this->store->store_id,
            'store_name' => $this->store->name,
            'status' => Order::parseApiStatus($this->status),
            'saving' => sprintf("%.02f", $saving),
            'goods' => $goods,
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
        ];
    }

    public function buildViewData()
    {
        $goods = [];
        $amount = 0.00;
        $saving = 0.00;

        if($this->goods){
            $goods = array_map(function($item) use (&$saving, &$amount){
                /* @var $item \api\models\OrderGoods */
                $saving += (float) ($item->goods->original_price - $item->price) * $item->quantity;
                $amount += (float) $item->amount;

                return [
                    'goods_id' => $item->goods->goods_id,
                    'goods_name' => $item->goods->name,
                    'preview' => Image::getImg($item->goods->preview, 300, 300, 'default.jpg'),
                    'original_price' => $item->goods->original_price,
                    'member_price' => $item->goods->member_price,
                    'quantity' => $item->quantity,
                    'attrs' => $item->parseAttr(),
                    'status' => $item->getApiStatus(),
                ];
            }, $this->goods);
        }

        return [
            'order_id' => $this->order_id,
            'store_id' => $this->store->store_id,
            'store_name' => $this->store->name,
            'status' => Order::parseApiStatus($this->status),
            'saving' => sprintf("%.02f", $saving),
            'fee' => $this->fee,
            'goods_amount' => sprintf("%.02f", $amount),
            'order_amount' => $this->amount,
            'goods' => $goods,
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'delivery_name' => $this->freight ? $this->freight->name : '全国包邮',
            'delivery' => [
                'consignee' => $this->consignee,
                'phone' => $this->phone,
                'address' => $this->address,
            ],
            'shipping' => [
                'last' => $this->isDelivery() ? '已发货' : '尚未发货',
                'url' => $this->isDelivery() ? 'https://m.kuaidi100.com/index_all.html?type='.$this->delivery_type.'&postid=' . $this->delivery_number : '',
            ],
            'payment_type' => $this->payment ? $this->payment->showPayType() : null,
        ];
    }

    public function isCancel()
    {
        return $this->status == self::STATUS_CANCEL;
    }

    public function isNew()
    {
        return $this->status == self::STATUS_NEW;
    }

    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    public function isRefund()
    {
        return $this->status == self::STATUS_REFUND;
    }

    public function isDelivery()
    {
        return $this->status == self::STATUS_DELIVERY;
    }

    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    public function cancel()
    {
        if($this->isNew()){
            $this->status = self::STATUS_CANCEL;

            return $this->save();
        }

        return false;
    }

    public function refund()
    {
        if($this->isPaid()){
            $this->status = self::STATUS_REFUND;

            return $this->save();
        }

        return false;
    }

    public function delivery()
    {
        if($this->isDelivery()){
            $this->status = self::STATUS_DONE;
//取消实时分配
//            if($this->store->is_offline){
//                $this->setOfflineReward();
//            }else{
//                $this->setReward();
//            }

            $this->setPerformance();

            OrderGoods::updateAll(['delivery_status' => OrderGoods::DELIVERY_DONE, 'status' => OrderGoods::STATUS_DONE], ['order_id' => $this->order_id]);

            return $this->save();
        }

        return false;
    }

    public function setPerformance()
    {
        $model = new Performance();

        if ($model->setAgent($this->user->referee)) {
            $model->order_id = $this->order_id;
            $model->amount = $this->amount;
            $model->is_offline = $this->is_offline;
            $model->year = date("Y", $this->created_at);
            $model->month = date("m", $this->created_at);
            $model->save();
        }
    }

    public function setReward()
    {
        $amount = $this->amount - $this->cost;

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $profit = $this->allocate($amount, $this->user->referee, UserIncome::RELATION_TYPE_USER);
            $profit = $this->allocate($profit, $this->store->referee, UserIncome::RELATION_TYPE_STORE, '店铺销售额分红');

            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollBack();

            return false;
        }
    }

    public function setOfflineReward()
    {
        return;
        $amount = $this->amount - $this->cost;
        $referee = $this->store->referee;

        if(!($amount > 0)){
            return false;
        }

        if(!$referee){
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $referee = User::findOne($referee);

            if(!$referee){
                throw new ErrorException('no referee');
            }

            if($referee->type == User::TYPE_COMPANY){
                $company = $referee;
            }else if($referee->company){
                $company = User::findOne($referee->company);
            }else{
                $company = null;
            }

            if($company) {
                $model = new UserIncome();
                $model->user_id = $company->user_id;
                $model->type = UserIncome::TYPE_COMPANY;
                $model->relation_id = $this->store_id;
                $model->relation_type = UserIncome::RELATION_TYPE_STORE;
                $model->amount = $amount * 0.05;
                $model->description = '线下店铺销售利润分成：推荐人公司！';
                $model->extend = Json::encode(['order_id' => $this->order_id]);
                $model->date = date("Y-m", $this->updated_at);
                $model->created_at = $this->updated_at;
                $model->updated_at = $this->updated_at;

                if(!$model->save()){
                    throw new Exception('推荐奖励更新失败！');
                }
            }

            $owner = User::find()->where(['store' => $this->store_id])->one();
            if($owner && $owner->area_id){
                $areaAgent = User::find()->where(['area_id' => $owner->area_id, 'type' => User::TYPE_CITY, 'status' => User::STATUS_ACTIVE])->one();

                if($areaAgent){
                    $model = new UserIncome();
                    $model->user_id = $areaAgent->user_id;
                    $model->type = UserIncome::TYPE_CITY;
                    $model->relation_id = $this->store_id;
                    $model->relation_type = UserIncome::RELATION_TYPE_STORE;
                    $model->amount = $amount * 0.05;
                    $model->description = '线下店铺销售利润分成：城市代理！';
                    $model->extend = Json::encode(['order_id' => $this->order_id]);
                    $model->date = date("Y-m", $this->updated_at);
                    $model->created_at = $this->updated_at;
                    $model->updated_at = $this->updated_at;

                    if(!$model->save()){
                        throw new Exception('推荐奖励更新失败！');
                    }
                }
            }

            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollBack();

            return false;
        }
    }

    public function paid(Payment $payment)
    {
        if($this->isPaid()){
            return;
        }

        foreach($this->goods as $goods){
            $goods->paid();
        }

        $this->payment_id = $payment->payment_id;
        $this->status = self::STATUS_PAID;

        if(!$this->save()){
            throw new Exception('订单更新失败！');
        }

        if($this->store->is_offline){
            $this->sync($payment);
        }
    }

    public function sync(Payment $payment)
    {
        return;
        if(!$this->store->pospal_app_id || !$this->store->pospal_app_key){
            return;
        }

        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal->setStore($this->store->pospal_app_id, $this->store->pospal_app_key);

        $params = [
            'payMethod' => ucfirst($payment->getPayMethon()),
            'customerNumber' => $this->user->open_id,
            'shippingFee' => $this->fee,
            'orderDateTime' => date("Y-m-d H:i:s", $this->created_at),
            'contactAddress' => $this->address,
            'contactName' => $this->consignee,
            'contactTel' => $this->phone,
            'orderSource' => 'openApi',
            'payOnLine' => 1,
            'totalAmount' => $this->amount,
            'items' => [],
        ];

        foreach($this->goods as $goods){
            $params['items'][] = [
                'productUid' => $goods->goods->pospal_id,
                'quantity' => $goods->quantity,
                'manualSellPrice' => $goods->price,
            ];
        }

        $result = $pospal->order->add($params);
        if($result->isSuccess()){
            $this->pospal_id = $result->getData('orderNo');

            if($this->freight_id == 0){
                $this->delivery_type = 'offline';
                $this->delivery_number = '线下送货';
                $this->status = self::STATUS_DELIVERY;

                OrderGoods::updateAll(['delivery_status' => OrderGoods::DELIVERY_WAITING, 'status' => OrderGoods::STATUS_DELIVERY], ['order_id' => $this->order_id]);
            }

            $this->save();
        }
    }

    public function allocate($amount, $referee, $type, $string = '非会员购物奖励')
    {
        if(!($amount > 0)){
            return 0;
        }

        if(!$referee){
            return 0;
        }

        $referee = User::findOne($referee);

        if(!$referee){
            return 0;
        }

        $profit = $amount;
        $relation_id = $type === UserIncome::RELATION_TYPE_USER ? $this->user_id : $this->store_id;
        $extend= [];
        if($type === UserIncome::RELATION_TYPE_USER){
            $extend = [
                'signup_at' => $this->user->created_at,
            ];
        }

        $extend['order_id'] = $this->order_id;

        $model = new UserIncome();
        $model->user_id = $referee->user_id;
        $model->type = UserIncome::TYPE_DIRECT;
        $model->relation_id = $relation_id;
        $model->relation_type = $type;
        $model->amount = $amount * 0.3;
        $model->description = '推荐' . $string. '！';
        $model->extend = Json::encode($extend);
        $model->date = date("Y-m", $this->updated_at);
        $model->created_at = $this->updated_at;
        $model->updated_at = $this->updated_at;

        if(!$model->save()){
            throw new Exception('推荐奖励更新失败！');
        }

        $profit = $profit - $model->amount;

        if($referee->referee){
            $topReferee = User::findOne($referee->referee);

            if($topReferee){
                $model = new UserIncome();
                $model->user_id = $topReferee->user_id;
                $model->type = UserIncome::TYPE_INDIRECT;
                $model->relation_id = $relation_id;
                $model->relation_type = $type;
                $model->amount = $amount * 0.1;
                $model->description = '间接推荐' . $string. '！';
                $model->extend = Json::encode($extend);
                $model->date = date("Y-m", $this->updated_at);
                $model->created_at = $this->updated_at;
                $model->updated_at = $this->updated_at;

                if(!$model->save()){
                    throw new Exception('推荐奖励更新失败！');
                }

                $profit = $profit - $model->amount;
            }
        }

        if($this->user->company){
            $companyReferee = User::findOne($this->user->company);

            if($companyReferee){
                $model = new UserIncome();
                $model->user_id = $companyReferee->user_id;
                $model->type = UserIncome::TYPE_COMPANY;
                $model->relation_id = $relation_id;
                $model->relation_type = $type;
                $model->amount = $amount * 0.2;
                $model->description = '公司推荐' . $string. '！';
                $model->extend = Json::encode($extend);
                $model->date = date("Y-m", $this->updated_at);
                $model->created_at = $this->updated_at;
                $model->updated_at = $this->updated_at;

                if(!$model->save()){
                    throw new Exception('推荐奖励更新失败！');
                }

                $profit = $profit - $model->amount;

                if($companyReferee->company){
                    $companyTopReferee = User::findOne($companyReferee->company);

                    if($companyTopReferee){
                        $model = new UserIncome();
                        $model->user_id = $companyTopReferee->user_id;
                        $model->type = UserIncome::TYPE_COMPANY;
                        $model->relation_id = $relation_id;
                        $model->relation_type = $type;
                        $model->amount = $amount * 0.05;
                        $model->description = '子公司推荐' . $string. '！';
                        $model->extend = Json::encode($extend);
                        $model->date = date("Y-m", $this->updated_at);
                        $model->created_at = $this->updated_at;
                        $model->updated_at = $this->updated_at;

                        if(!$model->save()){
                            throw new Exception('推荐奖励更新失败！');
                        }

                        $profit = $profit - $model->amount;
                    }
                }
            }
        }

        return $profit;
    }

    public static function parseStatus($status)
    {
        return isset(self::$_status[$status]) ? self::$_status[$status] : '';
    }

    public static function parseApiStatus($status)
    {
        return isset(self::$_api_status[$status]) ? self::$_api_status[$status] : '';
    }

    /**
     * 生成唯一订单号
     *
     * @param string $time
     *
     * @return string
     */
    public static function genId($time = '')
    {
        if(!$time){
            $time = time();
        }

        $id = sprintf("%02d%02d%d%02d%05d", date("y", $time) + date("N", $time), date("W", $time) + date("N", $time), date("N", $time), date("H", $time) + date("i", $time), Utils::getRand(5, true));

        if(self::find()->where(['order_id' => $id])->exists()){
            $id = self::genId();
        }

        return $id;
    }
}
