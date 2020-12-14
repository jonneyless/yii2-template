<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%store}}".
 *
 * @property string $store_id ID
 * @property int $is_offline 线下店铺
 * @property string $referee 推广人
 * @property string $owner 店主 ID
 * @property string $name 名称
 * @property string $preview 主图
 * @property string $service_phone 客服电话
 * @property string $service_qq 客服QQ
 * @property string $content 详情
 * @property string $pospal_app_id 银豹 App Id
 * @property string $pospal_app_key 银豹 App Key
 * @property string $pospal_normal_member 银豹标准会员 ID
 * @property string $pospal_vip_member 银豹VIP会员 ID
 * @property string $address 店铺地址
 * @property string $latitude 纬度
 * @property string $longitude 经度
 * @property int $created_at 添加时间
 * @property int $updated_at 修改时间
 * @property int $status 状态
 */
class Store extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%store}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_offline', 'referee', 'owner', 'created_at', 'updated_at', 'status'], 'integer'],
            [['name'], 'required'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['service_phone', 'service_qq', 'address'], 'string', 'max' => 255],
            [['pospal_app_id', 'pospal_app_key', 'pospal_normal_member', 'pospal_vip_member'], 'string', 'max' => 60],
            [['latitude', 'longitude'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_id' => 'ID',
            'is_offline' => '线下店铺',
            'referee' => '推广人',
            'owner' => '店主',
            'name' => '名称',
            'preview' => '主图',
            'service_phone' => '客服电话',
            'service_qq' => '客服QQ',
            'content' => '详情',
            'pospal_app_id' => '银豹 App Id',
            'pospal_app_key' => '银豹 App Key',
            'pospal_normal_member' => '银豹标准会员 ID',
            'pospal_vip_member' => '银豹VIP会员 ID',
            'address' => '店铺地址',
            'latitude' => '纬度',
            'longitude' => '经度',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'status' => '状态',
        ];
    }
}
