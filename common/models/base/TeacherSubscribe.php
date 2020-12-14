<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%teacher_subscribe}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 用户 ID
 * @property string $name 姓名
 * @property string $phone 电话
 * @property string $subscribe_at 预约时间
 * @property int $created_at 预约时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class TeacherSubscribe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teacher_subscribe}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'user_id', 'name', 'phone', 'subscribe_at'], 'required'],
            [['teacher_id', 'user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['subscribe_at'], 'safe'],
            [['name'], 'string', 'max' => 20],
            [['phone'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teacher_id' => '老师 ID',
            'user_id' => '用户 ID',
            'name' => '姓名',
            'phone' => '电话',
            'subscribe_at' => '预约时间',
            'created_at' => '预约时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
