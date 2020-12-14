<?php

namespace api\models;

use libs\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 用户收货地址数据模型
 *
 * {@inheritdoc}
 *
 * @property $area_line;
 * @property $full_address;
 */
class UserAddress extends \common\models\UserAddress
{

    public $area_line;
    public $full_address;

    public function fields()
    {
        return [
            'address_id',
            'consignee',
            'phone',
            'area_id',
            'address',
            'latitude',
            'longitude',
            'area_line',
            'full_address',
            'is_default',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['consignee', 'trim'],
            ['address', 'trim'],
            ['phone', 'trim'],
            ['latitude', 'trim'],
            ['longitude', 'trim'],
            ['area_id', 'default', 'value' => 0],
            ['is_default', 'default', 'value' => self::IS_DEFAULT_NO],
            ['is_default', 'in', 'range' => [self::IS_DEFAULT_NO, self::IS_DEFAULT_YES]],
        ]);
    }

    public function beforeSave($insert)
    {
        $this->phone = str_replace(" ", "", $this->phone);
        $this->address = str_replace("(null)", "", $this->address);

        if(!Yii::$app->user->identity->address){
            $this->is_default = self::IS_DEFAULT_YES;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->is_default == self::IS_DEFAULT_YES){
            UserAddress::updateAll(['is_default' => self::IS_DEFAULT_NO], [
                'and',
                ['=', 'user_id', Yii::$app->user->id],
                ['<>', 'address_id', $this->address_id],
                ['=', 'is_default', self::IS_DEFAULT_YES],
            ]);
        }

        $this->area_line = Area::getAreaLine($this->area_id, ' ');
        $this->full_address = $this->getFullAddress();
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->address_id = (int) $this->address_id;
        $this->area_id = (int) $this->area_id;
        $this->area_line = Area::getAreaLine($this->area_id, ' ');
        $this->full_address = $this->getFullAddress();
    }

    public static function setFilter($query, $params)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query->andWhere(['user_id' => Yii::$app->user->id]);

        if(isset($params['lng']) && $params['lng'] && isset($params['lat']) && $params['lat']){
            $point = Utils::squarePoint($params['lng'], $params['lat']);
            $query->andFilterWhere([
                'and',
                ['>', 'latitude', $point['rb']['lat']],
                ['<', 'latitude', $point['lt']['lat']],
                ['>', 'longitude', $point['lt']['lng']],
                ['<', 'longitude', $point['rb']['lng']],
            ]);
        }

        return $query;
    }

    public function getTopAreaId()
    {
        if(!$this->area_id){
            return 0;
        }

        $parent_ids = Area::getParentLine($this->area_id);

        return current($parent_ids);
    }

    public function getFullAddress()
    {
        if(!$this->area_id){
            return $this->address;
        }

        return Area::getAreaLine($this->area_id) . $this->address;
    }
}
