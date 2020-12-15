<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin_role}}".
 *
 * @property int $id 角色 ID
 * @property string $name 名称
 * @property string $description 说明
 * @property string|null $auth 权限
 * @property string|null $route 路由
 * @property int $status 状态
 */
class AdminRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['auth', 'route'], 'string'],
            [['status'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '角色 ID',
            'name' => '名称',
            'description' => '说明',
            'auth' => '权限',
            'route' => '路由',
            'status' => '状态',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_role}}';
    }
}
