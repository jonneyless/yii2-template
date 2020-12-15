<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id 用户 ID
 * @property string $username 用户名
 * @property string $password_hash 登录密码
 * @property string|null $password_reset_token 密码重置 Token
 * @property string|null $verification_token 验证 Token
 * @property string $email 邮箱
 * @property string $auth_key 登录保持密钥
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'auth_key'], 'required'],
            [['created_at', 'updated_at', 'status'], 'integer'],
            [['username'], 'string', 'max' => 30],
            [['password_hash', 'password_reset_token', 'verification_token'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['verification_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户 ID',
            'username' => '用户名',
            'password_hash' => '登录密码',
            'password_reset_token' => '密码重置 Token',
            'verification_token' => '验证 Token',
            'email' => '邮箱',
            'auth_key' => '登录保持密钥',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }
}
