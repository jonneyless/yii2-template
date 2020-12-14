<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property string $message_id 消息 ID
 * @property string $admin_id 管理员 ID
 * @property string $type 类型
 * @property string $title 标题
 * @property string $content 内容
 * @property int $is_all 全局发送
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['admin_id', 'is_all', 'created_at', 'updated_at', 'status'], 'integer'],
            [['type', 'title'], 'required'],
            [['content'], 'string'],
            [['type'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'message_id' => '消息 ID',
            'admin_id' => '管理员 ID',
            'type' => '类型',
            'title' => '标题',
            'content' => '内容',
            'is_all' => '全局发送',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
