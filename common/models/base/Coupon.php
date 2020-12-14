<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%coupon}}".
 *
 * @property string $id 页面 ID
 * @property string $user_id 用户 ID
 * @property string $code 标题
 * @property int $month 月数
 * @property int $day 天数
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Coupon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%coupon}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'month', 'day', 'created_at', 'updated_at', 'status'], 'integer'],
            [['code'], 'required'],
            [['code'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '页面 ID',
            'user_id' => '用户 ID',
            'code' => '券码',
            'month' => '月数',
            'day' => '天数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
