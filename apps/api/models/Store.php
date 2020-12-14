<?php

namespace api\models;

use ijony\helpers\Image;
use libs\Utils;
use Yii;

/**
 * 店铺数据模型
 *
 * {@inheritdoc}
 *
 * @property \api\models\Goods[]        $goods
 * @property \api\models\Favorite[]     $favorite
 * @property \api\models\StoreFreight[] $freight
 */
class Store extends \common\models\Store
{

    /**
     * 店铺商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasMany(Goods::className(), ['store_id' => 'store_id'])->andWhere(['status' => Goods::STATUS_ACTIVE]);
    }

    public function getFavorite()
    {
        return $this->hasMany(UserFavorite::className(), ['relation_id' => 'store_id'])->andWhere(['type' => 1]);
    }

    /**
     * 店铺物流模板
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFreight()
    {
        return $this->hasMany(StoreFreight::className(), ['store_id' => 'store_id']);
    }

    public static function findByAppId($appId)
    {
        return static::findOne(['pospal_app_id' => $appId]);
    }

    public function buildListData($lng, $lat)
    {
        return [
            'store_id'       => (int)$this->store_id,
            'name'           => $this->name,
            'preview'        => Image::getImg($this->preview, 375, 230, 'default.jpg'),
            'address'        => $this->address,
            'longitude'      => $this->longitude,
            'latitude'       => $this->latitude,
            'distance'       => $this->getDistance($lng, $lat),
            'goods_count'    => $this->getGoods()->count(),
            'favorite_count' => $this->getFavorite()->count(),
        ];
    }

    public function buildViewData()
    {
        return [
            'store_id'       => (int)$this->store_id,
            'name'           => $this->name,
            'preview'        => Image::getImg($this->preview, 750, 460, 'default.jpg'),
            'service_phone'  => $this->service_phone,
            'service_qq'     => $this->service_qq,
            'content'        => $this->content,
            'address'        => $this->address,
            'longitude'      => $this->longitude,
            'latitude'       => $this->latitude,
            'distance'       => $this->getDistance(),
            'goods_count'    => $this->getGoods()->count(),
            'favorite_count' => $this->getFavorite()->count(),
        ];
    }

    public function getDistance($lng = 0, $lat = 0)
    {
        if($lng && $lat){
            $distance = Utils::getDistance($lng, $lat, $this->longitude, $this->latitude);

            return $distance > 3 ? null : Utils::formatDistance($distance);
        }

        return '未知';
    }

    public function checkDistance($lng, $lat)
    {
        if(!$this->longitude || !$this->latitude){
            return true;
        }

        if(!$lng || !$lat){
            return false;
        }

        $distance = Utils::getDistance($lng, $lat, $this->longitude, $this->latitude);

        if($distance > 3){
            return false;
        }

        return true;
    }

    public function getNearest()
    {
        if(!Yii::$app->user->getIsGuest()){
            $query = UserAddress::find();
            $query = UserAddress::setFilter($query, ['lng' => $this->longitude, 'lat' => $this->latitude]);
            $query->orderBy('ACOS(SIN((' . $this->latitude . ' * 3.1415) / 180 ) * SIN((latitude * 3.1415) / 180 ) + COS((' . $this->latitude.' * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS((' . $this->longitude . ' * 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380 asc');

            /* @var \api\models\UserAddress $address */
            $address = $query->one();

            if($address){
                return [
                    'address_id' => $address->address_id,
                    'consignee' => $address->consignee,
                    'phone' => $address->phone,
                    'longitude' => $address->longitude,
                    'latitude' => $address->latitude,
                    'address' => $address->address,
                ];
            }
        }

        return null;
    }

    public function getFreeExpress()
    {
        $freight = StoreFreight::find()->where(['store_id' => $this->store_id])->orderBy(['free' => SORT_DESC])->one();

        if(!$freight){
            return '全国包邮';
        }

        return '满' . $freight->free . '包邮';
    }

    public function getDeliveryFreight($area_id, $feeAmount, $freeAmount)
    {
        $return = [];

        $amount = $feeAmount + $freeAmount;

        if(!$this->freight || !($feeAmount > 0)){
            $return[] = [
                'freight_id' => 0,
                'name'       => $this->is_offline ? '线下送货' : '全国包邮',
                'fee'        => 0.00,
                'free'       => 0.00,
                'freight'    => 0.00,
                'amount'     => sprintf('%.2f', $amount),
            ];
        }else{
            foreach($this->freight as $freight){
                $return[] = $freight->getDeliveryData($area_id, $feeAmount, $freeAmount);
            }
        }

        return $return;
    }
}
