<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;

/**
 * 订单商品数据模型
 *
 * {@inheritdoc}
 *
 * @property \api\models\Goods $goods
 * @property \api\models\Comment $comment
 */
class OrderGoods extends \common\models\OrderGoods
{

    const API_PAYMENT_REFUND_DONE = 'refunded';
    const API_PAYMENT_REFUND = 'refund';
    const API_PAYMENT_NO = 'unpaid';
    const API_PAYMENT_DONE = 'paid';

    const API_DELIVERY_NO = 'undelivery';
    const API_DELIVERY_WAITING = 'delivery';
    const API_DELIVERY_DONE = 'receive';
    const API_DELIVERY_CHANGE_DONE = 'changed';
    const API_DELIVERY_CHANGE = 'change';
    const API_DELIVERY_REFUND_DONE = 'returned';
    const API_DELIVERY_REFUND = 'return';

    const API_STATUS_CANCEL = 'cancel';
    const API_STATUS_NEW = 'unpaid';
    const API_STATUS_PAID = 'paid';
    const API_STATUS_DELIVERY = 'delivery';
    const API_STATUS_DONE = 'done';
    const API_STATUS_COMMENT = 'comment';

    private static $_payment_status = [
        self::API_PAYMENT_NO => self::PAYMENT_NO,
        self::API_PAYMENT_REFUND_DONE => self::PAYMENT_REFUND_DONE,
        self::API_PAYMENT_REFUND => self::PAYMENT_REFUND,
        self::API_PAYMENT_DONE => self::PAYMENT_DONE,
    ];

    private static $_api_payment_status = [
        self::PAYMENT_NO => self::API_PAYMENT_NO,
        self::PAYMENT_REFUND_DONE => self::API_PAYMENT_REFUND_DONE,
        self::PAYMENT_REFUND => self::API_PAYMENT_REFUND,
        self::PAYMENT_DONE => self::API_PAYMENT_DONE,
    ];

    private static $_delivery_status = [
        self::API_DELIVERY_NO => self::DELIVERY_NO,
        self::API_DELIVERY_WAITING => self::DELIVERY_WAITING,
        self::API_DELIVERY_DONE => self::DELIVERY_DONE,
        self::API_DELIVERY_CHANGE_DONE => self::DELIVERY_CHANGE_DONE,
        self::API_DELIVERY_CHANGE => self::DELIVERY_CHANGE,
        self::API_DELIVERY_REFUND_DONE => self::DELIVERY_REFUND_DONE,
        self::API_DELIVERY_REFUND => self::DELIVERY_REFUND,
    ];

    private static $_api_delivery_status = [
        self::DELIVERY_NO => self::API_DELIVERY_NO,
        self::DELIVERY_WAITING => self::API_DELIVERY_WAITING,
        self::DELIVERY_DONE => self::API_DELIVERY_DONE,
        self::DELIVERY_CHANGE_DONE => self::API_DELIVERY_CHANGE_DONE,
        self::DELIVERY_CHANGE => self::API_DELIVERY_CHANGE,
        self::DELIVERY_REFUND_DONE => self::API_DELIVERY_REFUND_DONE,
        self::DELIVERY_REFUND => self::API_DELIVERY_REFUND,
    ];

    private static $_status = [
        self::API_STATUS_CANCEL => self::STATUS_CANCEL,
        self::API_STATUS_NEW => self::STATUS_NEW,
        self::API_STATUS_PAID => self::STATUS_PAID,
        self::API_STATUS_DELIVERY => self::STATUS_DELIVERY,
        self::API_STATUS_DONE => self::STATUS_DONE,
        self::API_STATUS_COMMENT => self::STATUS_COMMENT,
    ];

    private static $_api_status = [
        self::STATUS_CANCEL => self::API_STATUS_CANCEL,
        self::STATUS_NEW => self::API_STATUS_NEW,
        self::STATUS_PAID => self::API_STATUS_PAID,
        self::STATUS_DELIVERY => self::API_STATUS_DELIVERY,
        self::STATUS_DONE => self::API_STATUS_DONE,
        self::STATUS_COMMENT => self::API_STATUS_COMMENT,
    ];

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['order_id' => 'order_id']);
    }

    public function getComment()
    {
        return $this->hasOne(Comment::className(), ['order_id' => 'order_id', 'goods_id' => 'goods_id']);
    }

    public function buildCommentListData()
    {
        return [
            'comment_id' => $this->comment ? $this->comment->comment_id : 0,
            'goods_id' => $this->goods_id,
            'order_id' => $this->order_id,
            'name' => $this->name,
            'preview' => Image::getImg($this->preview, 300, 300, 'default.jpg'),
            'attrs' => $this->parseAttr(),
            'status' => self::parseApiCommentStatus($this->status),
        ];
    }

    public function buildCommentViewData()
    {
        $comment = Comment::find()->where(['order_id' => $this->order_id, 'goods_id' => $this->goods_id, 'user_id' => Yii::$app->user->id])->one();

        return [
            'comment_id' => $comment->comment_id,
            'goods_score' => $comment->goods_score,
            'store_score' => $comment->store_score,
            'delivery_score' => $comment->delivery_score,
            'content' => $comment->content,
            'goods' => [
                'goods_id' => $this->goods_id,
                'name' => $this->name,
                'preview' => Image::getImg($this->preview, 300, 300, 'default.jpg'),
                'attrs' => $this->parseAttr(),
            ],
            'status' => self::parseApiCommentStatus($this->status),
        ];
    }

    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    public function isComment()
    {
        return $this->status == self::STATUS_COMMENT;
    }

    public function paid()
    {
        if($this->isPaid()){
            return;
        }

        $this->goods->updateSell($this->quantity);

        $this->payment_status = self::PAYMENT_DONE;
        $this->status = self::STATUS_PAID;

        if(!$this->save()){
            throw new Exception('订单商品更新失败！');
        }
    }

    public function comment()
    {
        $this->status = self::STATUS_COMMENT;

        if($this->goods->updateScore()){
            return $this->save();
        }

        Yii::error($this->goods->getErrors());

        return false;
    }

    public function getApiStatus()
    {
        switch($this->delivery_status){
            case self::DELIVERY_REFUND:
            case self::DELIVERY_CHANGE:
                return 'service';
            case self::DELIVERY_REFUND_DONE:
                return 'returned';
            default:
                return 'normal';
                break;
        }
    }

    public function parseAttr()
    {
        if($this->attrs){
            $attrs = Json::decode($this->attrs);

            foreach($attrs as $name => &$value){
                $value = $name . "：" . $value;
            }

            sort($attrs);

            return $attrs;
        }

        return [];
    }

    public static function parsePaymentStatus($status)
    {
        return isset(self::$_payment_status[$status]) ? self::$_payment_status[$status] : '';
    }

    public static function parseApiPaymentStatus($status)
    {
        return isset(self::$_api_payment_status[$status]) ? self::$_api_payment_status[$status] : '';
    }

    public static function parseDeliveryStatus($status)
    {
        return isset(self::$_delivery_status[$status]) ? self::$_delivery_status[$status] : '';
    }

    public static function parseApiDeliveryStatus($status)
    {
        return isset(self::$_api_delivery_status[$status]) ? self::$_api_delivery_status[$status] : '';
    }

    public static function parseStatus($status)
    {
        return isset(self::$_status[$status]) ? self::$_status[$status] : '';
    }

    public static function parseApiStatus($status)
    {
        return isset(self::$_api_status[$status]) ? self::$_api_status[$status] : '';
    }

    public static function parseCommentStatus($status)
    {
        if(!$status){
            return '';
        }else if($status == self::API_STATUS_COMMENT){
            return self::STATUS_COMMENT;
        }else{
            return self::STATUS_DONE;
        }
    }

    public static function parseApiCommentStatus($status)
    {
        if($status == self::STATUS_COMMENT){
            return self::API_STATUS_COMMENT;
        }else{
            return 'none';
        }
    }
}
