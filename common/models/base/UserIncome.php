<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%user_income}}".
 *
 * @property int $id id
 * @property string $user_id 用户 ID
 * @property string $type 类型
 * @property string $relation_id 关联 ID
 * @property string $relation_type 关联类型
 * @property string $amount 金额
 * @property string $description 说明
 * @property string $extend 扩展数据
 * @property string $date 年月
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $synced 是否同步
 */
class UserIncome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_income}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'relation_id'], 'required'],
            [['user_id', 'relation_id', 'created_at', 'updated_at', 'synced'], 'integer'],
            [['amount'], 'number'],
            [['type', 'relation_type'], 'string', 'max' => 20],
            [['description', 'extend'], 'string', 'max' => 255],
            [['date'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'user_id' => '用户 ID',
            'type' => '类型',
            'relation_id' => '关联 ID',
            'relation_type' => '关联类型',
            'amount' => '金额',
            'description' => '说明',
            'extend' => '扩展数据',
            'date' => '年月',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'synced' => '是否同步',
        ];
    }
}
