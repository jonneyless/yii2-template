<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%comment_image}}".
 *
 * @property string $comment_id 商品 ID
 * @property string $image 图片
 */
class CommentImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment_image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment_id', 'image'], 'required'],
            [['comment_id'], 'integer'],
            [['image'], 'string', 'max' => 150],
            [['comment_id', 'image'], 'unique', 'targetAttribute' => ['comment_id', 'image']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => '商品 ID',
            'image' => '图片',
        ];
    }
}
