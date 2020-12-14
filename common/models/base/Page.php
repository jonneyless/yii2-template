<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property string $id 页面 ID
 * @property string $title 标题
 * @property string $content 内容
 * @property int $status 状态
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['status'], 'integer'],
            [['title'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '页面 ID',
            'title' => '标题',
            'content' => '内容',
            'status' => '状态',
        ];
    }
}
