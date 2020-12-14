<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $consignee
 * @property string $area_id
 * @property string $address
 * @property string $phone
 * @property integer $is_default
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'consignee', 'area_id', 'address', 'phone'], 'required'],
            [['user_id', 'area_id', 'is_default'], 'integer'],
            [['phone'], 'string', 'max' => 60],
            [['consignee'], 'string', 'max' => 30],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '地址 ID',
            'user_id' => '用户 ID',
            'consignee' => '收货人',
            'area_id' => '地区 ID',
            'address' => '详细地址',
            'phone' => '联系电话',
            'is_default' => '默认',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }
}
