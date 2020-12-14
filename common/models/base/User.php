<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $user_id ID
 * @property string $open_id 外部平台 ID
 * @property string $area_id 地区 ID
 * @property int $type 类型
 * @property string $referee 推荐人
 * @property string $company 所属公司
 * @property string $store 所属店铺
 * @property string $username 用户名
 * @property string $auth_key 密钥
 * @property string $access_token 登录 Token
 * @property string $device 设备 Token
 * @property string $device_type 设备类型
 * @property string $password_hash 密码
 * @property string $tradepass_hash 交易密码
 * @property string $avatar 头像
 * @property string $mobile 手机号码
 * @property string $amount 账户金额
 * @property string $debt 欠款
 * @property int $created_at 注册时间
 * @property int $updated_at 登录时间
 * @property int $expire_at 过期时间
 * @property int $status 状态
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area_id', 'type', 'referee', 'company', 'store', 'created_at', 'updated_at', 'expire_at', 'status'], 'integer'],
            [['username', 'auth_key', 'access_token', 'password_hash', 'mobile'], 'required'],
            [['amount', 'debt'], 'number'],
            [['open_id', 'device_type'], 'string', 'max' => 64],
            [['username', 'access_token', 'device', 'password_hash', 'tradepass_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['avatar'], 'string', 'max' => 150],
            [['mobile'], 'string', 'max' => 60],
            [['username'], 'unique'],
            [['mobile'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID',
            'open_id' => '外部平台 ID',
            'area_id' => '地区 ID',
            'type' => '类型',
            'referee' => '推荐人',
            'company' => '所属公司',
            'store' => '所属店铺',
            'username' => '用户名',
            'auth_key' => '密钥',
            'access_token' => '登录 Token',
            'device' => '设备 Token',
            'device_type' => '设备类型',
            'password_hash' => '密码',
            'tradepass_hash' => '交易密码',
            'avatar' => '头像',
            'mobile' => '手机号码',
            'amount' => '账户金额',
            'debt' => '欠款',
            'created_at' => '注册时间',
            'updated_at' => '登录时间',
            'expire_at' => '过期时间',
            'status' => '状态',
        ];
    }
}
