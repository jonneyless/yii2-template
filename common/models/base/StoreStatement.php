<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%store_statement}}".
 *
 * @property int $store_id 店铺 ID
 * @property string $date 年月
 * @property string $offline 线下利润
 * @property string $online 线上利润
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class StoreStatement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%store_statement}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'date'], 'required'],
            [['store_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['offline', 'online'], 'number'],
            [['date'], 'string', 'max' => 10],
            [['store_id', 'date'], 'unique', 'targetAttribute' => ['store_id', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'store_id' => '店铺 ID',
            'date' => '年月',
            'offline' => '线下利润',
            'online' => '线上利润',
            'created_at' => '生成时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }
}
