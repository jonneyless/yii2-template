<?php

namespace common\models;

use libs\SMS;
use libs\Utils;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $goods_id
 * @property string $group_id
 * @property string $price
 * @property string $quantity
 * @property string $amount
 * @property string $paid
 * @property string $consignee
 * @property string $area_id
 * @property string $address
 * @property string $phone
 * @property string $delivery_name
 * @property string $delivery_number
 * @property string $pay_card
 * @property integer $is_first
 * @property string $created_at
 * @property string $updated_at
 * @property integer $payment_status
 * @property integer $delivery_status
 * @property integer $status
 *
 * @property string $vcode
 * @property string $student
 *
 * @property \common\models\User $user
 * @property \common\models\Goods $goods
 * @property \common\models\Group $group
 * @property \common\models\GoodsVirtual $virtual
 *
 * @const 默认   PAYMENT_NO
 * @const 已退款 PAYMENT_REFUND_DONE
 * @const 待支付 PAYMENT_WAITING
 * @const 待退款 PAYMENT_REFUND
 * @const 已支付 PAYMENT_DONE
 *
 * @const 默认   DELIVERY_NO
 * @const 已退货 DELIVERY_REFUND_DONE
 * @const 待收货 DELIVERY_WAITING
 * @const 待退货 DELIVERY_REFUND
 * @const 已收货 DELIVERY_DONE
 */
class Order extends namespace\base\Order
{
    public $vcode;
    public $student;

    const IS_FIRST_NO = 0;
    const IS_FIRST_YES = 1;

    const STATUS_CANCEL = 0;
    const STATUS_NEW = 1;
    const STATUS_PAID = 2;
    const STATUS_DELIVERY = 3;
    const STATUS_REFUND = 4;
    const STATUS_DONE = 9;

    const PAYMENT_NO = 0;
    const PAYMENT_REFUND_DONE = 1;
    const PAYMENT_WAITING = 2;
    const PAYMENT_REFUND = 8;
    const PAYMENT_DONE = 9;

    const DELIVERY_NO = 0;
    const DELIVERY_REFUND_DONE = 1;
    const DELIVERY_WAITING = 2;
    const DELIVERY_REFUND = 8;
    const DELIVERY_DONE = 9;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['vcode', 'student'], 'string'],
            ['is_first', 'default', 'value' => self::IS_FIRST_NO],
            ['is_first', 'in', 'range' => [self::IS_FIRST_NO, self::IS_FIRST_YES]],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_CANCEL, self::STATUS_NEW, self::STATUS_PAID, self::STATUS_DELIVERY, self::STATUS_DONE]],
            ['payment_status', 'default', 'value' => self::PAYMENT_NO],
            ['payment_status', 'in', 'range' => [self::PAYMENT_NO, self::PAYMENT_REFUND_DONE, self::PAYMENT_WAITING, self::PAYMENT_REFUND, self::PAYMENT_DONE]],
            ['delivery_status', 'default', 'value' => self::DELIVERY_NO],
            ['delivery_status', 'in', 'range' => [self::DELIVERY_NO, self::DELIVERY_REFUND_DONE, self::DELIVERY_WAITING, self::DELIVERY_REFUND, self::DELIVERY_DONE]],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => '订单 ID',
            'user_id' => '用户 ID',
            'goods_id' => '商品 ID',
            'group_id' => '拼单 ID',
            'price' => '单价',
            'quantity' => '数量',
            'amount' => '总金额',
            'paid' => '已付金额',
            'consignee' => '收货人',
            'area_id' => '地址区域',
            'address' => '收货地址',
            'phone' => '联系电话',
            'delivery_name' => '物流名称',
            'delivery_number' => '物流单号',
            'pay_card' => '支付卡号',
            'is_first' => '角色',
            'is_virtual' => '虚拟卡',
            'group_status' => '拼单状态',
            'created_at' => '下单时间',
            'updated_at' => '更新时间',
            'payment_status' => '支付状态',
            'delivery_status' => '物流状态',
            'status' => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->delivery_name && $this->delivery_number) {
            $this->status = Order::STATUS_DELIVERY;
            $this->delivery_status = Order::DELIVERY_WAITING;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            if ($this->group->one_delivery == 1) {
                if (isset($changedAttributes['delivery_status'])) {
                    Order::updateAll(['delivery_status' => $this->delivery_status, 'status' => Order::STATUS_DELIVERY], ['group_id' => $this->group_id, 'status' => Order::STATUS_PAID]);
                }
            }
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        $expire = Yii::$app->params['order.pay.expire'] * 60;

        if ($this->status == self::STATUS_NEW) {
            if ($this->created_at + $expire < time()) {
                $this->setCancel();
            }
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getVirtual()
    {
        if ($this->group->status != Group::STATUS_OVER) {
            return [];
        }

        return $this->hasMany(GoodsVirtual::className(), ['order_id' => 'id']);
    }

    public function getOrderUrl()
    {
        return Url::to(['user/order-view', 'id' => $this->id]);
    }

    public function getCancelUrl()
    {
        return Url::to(['user/order-cancel', 'id' => $this->id]);
    }

    public function getPayUrl()
    {
        return Url::to(['site/pay', 'order' => $this->id]);
    }

    public function checkMobile()
    {
        $user = User::find()->where(['mobile' => $this->phone])->one();
        if (!$user) {
            return true;
        }

        $orders = Order::find()->where(['group_id' => $this->group_id, 'user_id' => $user->id, 'status' => [self::STATUS_NEW, self::STATUS_PAID, self::STATUS_DELIVERY, self::STATUS_DONE]])->count();

        return $orders > 0 ? false : true;
    }

    public function setCancel()
    {
        if ($this->payment_status == self::PAYMENT_DONE) {
            $this->payment_status = self::PAYMENT_REFUND;
        }

        $this->status = self::STATUS_CANCEL;

        return $this->save();
    }

    public function setDelivery()
    {
        if ($this->status == self::STATUS_NEW) {
            $this->status = self::STATUS_CANCEL;
        }

        if ($this->status != self::STATUS_CANCEL) {
            if ($this->goods->is_virtual == 1) {
                $this->status = self::STATUS_DONE;
                $this->delivery_status = self::DELIVERY_DONE;
            }
        }

        $this->save();
    }

    public function setRefundDone()
    {
        $this->payment_status = Order::PAYMENT_REFUND_DONE;

        return $this->save();
    }

    public function showStatus()
    {
        $return = [];

        switch ($this->status) {
            default:
            case self::STATUS_CANCEL:

                $return[] = Html::tag('span', '已失效', ['class' => 'gray']);

                switch ($this->payment_status) {
                    case self::PAYMENT_REFUND:
                        $return[] = Html::tag('span', '待退款', ['class' => 'black']);
                        break;
                    case self::PAYMENT_REFUND_DONE:
                        $return[] = Html::tag('span', '已退款', ['class' => 'gray']);
                        break;
                    default:
                        break;
                }

                return join(", ", $return);

                break;
            case self::STATUS_NEW:

                switch ($this->payment_status) {
                    default:
                    case self::PAYMENT_NO:
                        return Html::tag('span', '待付款', ['class' => 'black']);
                        break;
                    case self::PAYMENT_WAITING:
                        return Html::tag('span', '支付中', ['class' => 'blue']);
                        break;
                }

                break;
            case self::STATUS_PAID:

                if ($this->group->status == Group::STATUS_OVER) {
                    $return[] = Html::tag('span', '已成团', ['class' => 'red']);
                    $return[] = Html::tag('span', '待发货', ['class' => 'black']);
                } else {
                    $return[] = Html::tag('span', '已支付', ['class' => 'red']);
                    $return[] = Html::tag('span', '待成团', ['class' => 'black']);
                }

                return join(", ", $return);

                break;
            case self::STATUS_DELIVERY:

                $return[] = Html::tag('span', '已成团', ['class' => 'red']);

                switch ($this->delivery_status) {
                    case self::DELIVERY_NO:
                    default:
                        $return[] = Html::tag('span', '待发货', ['class' => 'black']);
                        break;
                    case self::DELIVERY_REFUND:
                        $return[] = Html::tag('span', '待退货', ['class' => 'black']);
                        break;
                    case self::DELIVERY_REFUND_DONE:
                        $return[] = Html::tag('span', '已退货', ['class' => 'gray']);
                        break;
                    case self::DELIVERY_WAITING:
                        $return[] = Html::tag('span', '已发货', ['class' => 'blue']);
                        break;
                    case self::DELIVERY_DONE:
                        $return[] = Html::tag('span', '已收货', ['class' => 'red']);
                        break;
                }

                return join(", ", $return);

                break;
            case self::STATUS_DONE:

                if ($this->goods->is_virtual == 1) {
                    $return[] = Html::tag('span', '已支付', ['class' => 'red']);
                    $return[] = Html::tag('span', '已成团', ['class' => 'red']);
                } else {
                    $return[] = Html::tag('span', '已成团', ['class' => 'red']);
                    $return[] = Html::tag('span', '已收货', ['class' => 'red']);
                }

                return join(", ", $return);

                break;
        }
    }

    public function showAreaLine()
    {
        $areas = Area::getParentLine($this->area_id);
        foreach ($areas as &$area) {
            $area = Area::getNameById($area);
        }

        return join("", $areas);
    }

    public static function genId($time = '')
    {
        if (!$time) {
            $time = time();
        }

        $id = sprintf("SZPT%02d%02d%d%02d%05d", date("y", $time) + date("N", $time), date("W", $time) + date("N", $time), date("N", $time), date("H", $time) + date("i", $time), Utils::getRand(5, true));

        if (self::find()->where(['id' => $id])->exists()) {
            $id = self::genId();
        }

        return $id;
    }

    public static function getStatusSelectData()
    {
        return [
            self::STATUS_CANCEL => '已失效',
            self::STATUS_NEW => '待支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_DELIVERY => '已发货',
            self::STATUS_REFUND => '已退货',
            self::STATUS_DONE => '已完成',
        ];
    }

    public static function getPaymentStatusSelectData()
    {
        return [
            self::PAYMENT_NO => '待支付',
            self::PAYMENT_REFUND_DONE => '已退款',
            self::PAYMENT_WAITING => '支付中',
            self::PAYMENT_REFUND => '待退款',
            self::PAYMENT_DONE => '已支付',
        ];
    }

    public static function getDeliveryStatusSelectData()
    {
        return [
            self::DELIVERY_NO => '待发货',
            self::DELIVERY_REFUND_DONE => '已退货',
            self::DELIVERY_WAITING => '已发货',
            self::DELIVERY_REFUND => '待退货',
            self::DELIVERY_DONE => '已收货',
        ];
    }

    public function showOrderStatus()
    {
        $status = [
            self::STATUS_CANCEL => '<span class="gray">已失效</span>',
            self::STATUS_NEW => '<span class="red">待支付</span>',
            self::STATUS_PAID => '<span class="blue">已支付</span>',
            self::STATUS_DELIVERY => '<span class="blue">已发货</span>',
            self::STATUS_DONE => '<span class="green">已完成</span>',
        ];

        return $status[$this->status];
    }

    public function showPaymentStatus()
    {
        if ($this->status == Order::STATUS_CANCEL && $this->payment_status == Order::PAYMENT_NO) {
            return '';
        }

        $status = [
            self::PAYMENT_NO => '<span class="red">待支付</span>',
            self::PAYMENT_REFUND_DONE => '<span class="gray">已退款</span>',
            self::PAYMENT_WAITING => '<span class="blue">支付中</span>',
            self::PAYMENT_REFUND => '<span class="red">待退款</span>',
            self::PAYMENT_DONE => '<span class="green">已支付</span>',
        ];

        return $status[$this->payment_status];
    }

    public function showDeliveryStatus()
    {
        if ($this->status == Order::STATUS_NEW || $this->status == Order::STATUS_CANCEL || $this->status == Order::STATUS_PAID) {
            return '';
        }

        $status = [
            self::DELIVERY_NO => '<span class="red">待发货</span>',
            self::DELIVERY_REFUND_DONE => '<span class="gray">已退货</span>',
            self::DELIVERY_WAITING => '<span class="blue">已发货</span>',
            self::DELIVERY_REFUND => '<span class="red">待退货</span>',
            self::DELIVERY_DONE => '<span class="green">已收货</span>',
        ];

        return $status[$this->delivery_status];
    }
}
