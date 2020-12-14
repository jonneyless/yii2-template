<?php

namespace api\models;

use ijony\helpers\Image;
use ijony\helpers\Url;
use ijony\helpers\Utils;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%service}}".
 *
 * {@inheritdoc}
 *
 * @property \api\models\OrderGoods $goods
 * @property \api\models\ServiceAttachment[] $attachs
 */
class Service extends \common\models\Service
{

    const API_TYPE_CHANGE = 'change';
    const API_TYPE_RETURN = 'return';

    const API_STATUS_CANCEL = 'cancel';
    const API_STATUS_NEW = 'new';
    const API_STATUS_WAITING = 'waiting';
    const API_STATUS_DONE = 'done';

    private static $_status = [
        self::API_STATUS_CANCEL => self::STATUS_CANCEL,
        self::API_STATUS_NEW => self::STATUS_NEW,
        self::API_STATUS_WAITING => self::STATUS_WAITING,
        self::API_STATUS_DONE => self::STATUS_DONE,
    ];

    private static $_api_status = [
        self::STATUS_CANCEL => self::API_STATUS_CANCEL,
        self::STATUS_NEW => self::API_STATUS_NEW,
        self::STATUS_WAITING => self::API_STATUS_WAITING,
        self::STATUS_DONE => self::API_STATUS_DONE,
    ];

    private static $_type = [
        self::API_TYPE_CHANGE => self::TYPE_CHANGE,
        self::API_TYPE_RETURN => self::TYPE_RETURN,
    ];

    private static $_api_type = [
        self::TYPE_CHANGE => self::API_TYPE_CHANGE,
        self::TYPE_RETURN => self::API_TYPE_RETURN,
    ];

    public function getGoods()
    {
        return $this->hasOne(OrderGoods::className(), ['order_id' => 'order_id', 'goods_id' => 'goods_id']);
    }

    public function getAttachs()
    {
        return $this->hasMany(ServiceAttachment::className(), ['service_id' => 'service_id']);
    }

    public function cancel()
    {
        $this->status = self::STATUS_CANCEL;

        return $this->save();
    }

    public function buildListData()
    {

        return [
            'serivce_id' => $this->service_id,
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'type' => self::parseApiType($this->type),
            'goods' => [
                'goods_id' => $this->goods_id,
                'goods_name' => $this->goods->name,
                'preview' => Image::getImg($this->goods->preview, 300, 300, 'default.jpg'),
                'original_price' => $this->goods->goods->original_price,
                'member_price' => $this->goods->price,
                'quantity' => $this->goods->quantity,
                'attrs' => $this->goods->parseAttr(),
            ],
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'status' => self::parseApiStatus($this->status),
            'detail' => Url::getFull('service-' . $this->service_id . '.html'),
        ];
    }

    public function buildViewData()
    {
        return [
            'serivce_id' => $this->service_id,
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'type' => self::parseApiType($this->type),
            'goods' => [
                'goods_id' => $this->goods_id,
                'goods_name' => $this->goods->name,
                'preview' => Image::getImg($this->goods->preview, 300, 300, 'default.jpg'),
                'original_price' => $this->goods->goods->original_price,
                'member_price' => $this->goods->price,
                'quantity' => $this->goods->quantity,
                'attrs' => $this->goods->parseAttr(),
            ],

            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'status' => self::parseApiStatus($this->status),
            'detail' => Url::getFull('service-' . $this->service_id . '.html'),
        ];
    }

    public function parseMemo()
    {
        $memo = Json::decode($this->memo);

        return join("<br><br>", $memo);
    }

    public static function parseStatus($status)
    {
        return isset(self::$_status[$status]) ? self::$_status[$status] : '';
    }

    public static function parseApiStatus($status)
    {
        return isset(self::$_api_status[$status]) ? self::$_api_status[$status] : '';
    }

    public static function parseType($type)
    {
        return isset(self::$_type[$type]) ? self::$_type[$type] : '';
    }

    public static function parseApiType($type)
    {
        return isset(self::$_api_type[$type]) ? self::$_api_type[$type] : '';
    }

    /**
     * 生成唯一订单号
     *
     * @return string
     */
    public static function genId()
    {
        $id = date("YmdHis", time()) . Utils::getRand(6, true);

        if(self::find()->where(['service_id' => $id])->exists()){
            $id = self::genId();
        }

        return $id;
    }
}
