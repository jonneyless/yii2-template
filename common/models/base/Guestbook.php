<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%guestbook}}".
 *
 * @property string $guestbook_id 留言 ID
 * @property string $user_id 用户 ID
 * @property string $type 类型
 * @property string $title 标题
 * @property string $content 内容
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 */
class Guestbook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%guestbook}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'required'],
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
            'guestbook_id' => '留言 ID',
            'user_id' => '用户 ID',
            'type' => '类型',
            'title' => '标题',
            'content' => '内容',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
        ];
    }
}
