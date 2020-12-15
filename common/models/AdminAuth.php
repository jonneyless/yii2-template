<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin_auth}}".
 *
 * @property string $key 标识
 * @property string $name 名称
 * @property string $parent 父级
 * @property string $description 说明
 * @property string $route 路由
 */
class AdminAuth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'name', 'route'], 'required'],
            [['route'], 'string'],
            [['key', 'parent'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 60],
            [['description'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => '标识',
            'name' => '名称',
            'parent' => '父级',
            'description' => '说明',
            'route' => '路由',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_auth}}';
    }
}
