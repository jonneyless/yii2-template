<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_favorite}}".
 *
 * @property string $user_id 用户 ID
 * @property int $type 类型
 * @property string $relation_id 关联 ID
 */
class UserFavorite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_favorite}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'relation_id'], 'required'],
            [['user_id', 'type', 'relation_id'], 'integer'],
            [['user_id', 'type', 'relation_id'], 'unique', 'targetAttribute' => ['user_id', 'type', 'relation_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户 ID',
            'type' => '类型',
            'relation_id' => '关联 ID',
        ];
    }
}
