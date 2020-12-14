<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property string $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $signup_at
 * @property integer $status
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key'], 'required'],
            [['created_at', 'updated_at', 'signup_at', 'status'], 'integer'],
            [['username'], 'string', 'max' => 24],
            [['password_hash'], 'string', 'max' => 64],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户 ID',
            'username' => '用户名',
            'password_hash' => '登录密码',
            'auth_key' => '登录保持密钥',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'signup_at' => '登录时间',
            'status' => '状态',
        ];
    }
}
