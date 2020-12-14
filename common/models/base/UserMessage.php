<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_message}}".
 *
 * @property string $message_id 消息 ID
 * @property string $user_id 用户 ID
 * @property int $is_read 已读
 */
class UserMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_id', 'user_id'], 'required'],
            [['message_id', 'user_id', 'is_read'], 'integer'],
            [['message_id', 'user_id'], 'unique', 'targetAttribute' => ['message_id', 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'message_id' => '消息 ID',
            'user_id' => '用户 ID',
            'is_read' => '已读',
        ];
    }
}
