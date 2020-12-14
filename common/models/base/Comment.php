<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property string $comment_id 评论 ID
 * @property string $order_id 订单号
 * @property string $goods_id 商品 ID
 * @property string $user_id 用户 ID
 * @property int $goods_score 商品评分
 * @property int $store_score 店铺评分
 * @property int $delivery_score 物流评分
 * @property string $content 描述
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'user_id'], 'required'],
            [['goods_id', 'user_id', 'goods_score', 'store_score', 'delivery_score', 'created_at', 'updated_at', 'status'], 'integer'],
            [['order_id'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => '评论 ID',
            'order_id' => '订单号',
            'goods_id' => '商品 ID',
            'user_id' => '用户 ID',
            'goods_score' => '商品评分',
            'store_score' => '店铺评分',
            'delivery_score' => '物流评分',
            'content' => '描述',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
