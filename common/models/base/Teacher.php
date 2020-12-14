<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%teacher}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 关联用户 ID
 * @property string $store_id 所属店铺 ID
 * @property string $name 名称
 * @property string $title 头衔
 * @property string $intro 介绍
 * @property string $avatar 头像
 * @property int $created_at 注册时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Teacher extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teacher}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'store_id', 'name'], 'required'],
            [['user_id', 'store_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['name', 'title'], 'string', 'max' => 30],
            [['intro', 'avatar'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teacher_id' => '老师 ID',
            'user_id' => '关联用户 ID',
            'store_id' => '所属店铺 ID',
            'name' => '名称',
            'title' => '头衔',
            'intro' => '介绍',
            'avatar' => '头像',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
