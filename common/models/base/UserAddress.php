<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_address}}".
 *
 * @property string $address_id 地址 ID
 * @property string $user_id 用户 ID
 * @property string $consignee 收货人
 * @property string $area_id 地区 ID
 * @property string $address 详细地址
 * @property string $latitude 纬度
 * @property string $longitude 经度
 * @property string $phone 联系电话
 * @property int $is_default 默认
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'consignee', 'address', 'phone'], 'required'],
            [['user_id', 'area_id', 'is_default'], 'integer'],
            [['consignee', 'latitude', 'longitude'], 'string', 'max' => 30],
            [['address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'address_id' => '地址 ID',
            'user_id' => '用户 ID',
            'consignee' => '收货人',
            'area_id' => '地区 ID',
            'address' => '详细地址',
            'latitude' => '纬度',
            'longitude' => '经度',
            'phone' => '联系电话',
            'is_default' => '默认',
        ];
    }
}
