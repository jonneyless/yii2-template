<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%teacher_auth}}".
 *
 * @property string $teacher_id 老师 ID
 * @property string $user_id 用户 ID
 * @property string $code 验证码
 * @property int $created_at 预约时间
 * @property int $updated_at 更新时间
 */
class TeacherAuth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'user_id', 'code'], 'required'],
            [['teacher_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 255],
            [['code'], 'unique'],
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
            'code' => '验证码',
            'created_at' => '预约时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teacher_auth}}';
    }
}
