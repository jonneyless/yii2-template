<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property string $service_id 售后编号
 * @property string $order_id 订单号
 * @property string $goods_id 商品 ID
 * @property string $user_id 用户 ID
 * @property int $type 类型
 * @property int $quantity 数量
 * @property string $memo 描述
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'order_id', 'goods_id', 'user_id'], 'required'],
            [['goods_id', 'user_id', 'type', 'quantity', 'created_at', 'updated_at', 'status'], 'integer'],
            [['service_id', 'order_id'], 'string', 'max' => 20],
            [['memo'], 'string', 'max' => 255],
            [['service_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_id' => '售后编号',
            'order_id' => '订单号',
            'goods_id' => '商品 ID',
            'user_id' => '用户 ID',
            'type' => '类型',
            'quantity' => '数量',
            'memo' => '描述',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
