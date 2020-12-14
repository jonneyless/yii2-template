<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%store_freight}}".
 *
 * @property string $freight_id 运费 ID
 * @property string $store_id 店铺 ID
 * @property string $name 名称
 * @property string $fee 默认费用
 * @property string $free 包邮额度
 * @property string $area_config 地区费用
 */
class StoreFreight extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%store_freight}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'name'], 'required'],
            [['store_id'], 'integer'],
            [['fee', 'free'], 'number'],
            [['area_config'], 'string'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'freight_id' => '运费 ID',
            'store_id' => '店铺 ID',
            'name' => '名称',
            'fee' => '默认费用',
            'free' => '包邮额度',
            'area_config' => '地区费用',
        ];
    }
}
