<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $id
 * @property string $password_hash
 * @property string $auth_key
 * @property string $name
 * @property string $mobile
 * @property string $created_at
 * @property string $updated_at
 * @property string $signup_at
 * @property string $first_pay
 * @property integer $is_vip
 * @property integer $sign_status
 * @property integer $login_status
 * @property integer $status
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'signup_at', 'first_pay', 'is_vip', 'sign_status', 'login_status', 'status'], 'integer'],
            [['auth_key'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['mobile'], 'string', 'max' => 13],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户 ID',
            'auth_key' => '登录保持密钥',
            'name' => '姓名',
            'mobile' => '手机号码',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'signup_at' => '登录时间',
            'first_pay' => '首次支付',
            'is_vip' => 'VIP 用户',
            'sign_status' => '签约状态',
            'login_status' => '登录状态',
            'status' => '状态',
        ];
    }
}
