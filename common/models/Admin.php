<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property int $id 用户 ID
 * @property int $role_id 角色 ID
 * @property string $username 用户名
 * @property string $password_hash 登录密码
 * @property string $auth_key 登录保持密钥
 * @property int $created_at 注册时间
 * @property int $updated_at 更新时间
 * @property int $signin_at 登录时间
 * @property int $status 状态
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'created_at', 'updated_at', 'signin_at', 'status'], 'integer'],
            [['username'], 'required'],
            [['username'], 'string', 'max' => 24],
            [['password_hash'], 'string', 'max' => 64],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户 ID',
            'role_id' => '角色 ID',
            'username' => '用户名',
            'password_hash' => '登录密码',
            'auth_key' => '登录保持密钥',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'signin_at' => '登录时间',
            'status' => '状态',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }
}
