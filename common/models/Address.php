<?php

namespace common\models;

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
class Address extends namespace\base\Address
{
    /**
     * @var 非默认
     */
    const IS_DEFAULT_NO = 0;
    /**
     * @var 默认
     */
    const IS_DEFAULT_YES = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'consignee', 'area_id', 'address', 'phone'], 'required'],
            [['user_id', 'is_default'], 'integer'],
            [['phone'], 'string', 'max' => 60],
            [['consignee'], 'string', 'max' => 30],
            [['address'], 'string', 'max' => 255],
            ['area_id', 'areaValidator'],
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
            'area_id' => '省市区',
            'address' => '详细地址',
            'phone' => '联系电话',
            'is_default' => '设为默认收货地址',
        ];
    }

    public function areaValidator()
    {
        $area_id = $this->area_id;

        if (!$area_id) {
            $this->addError('area_id', '请选择省市区！');

            return false;
        }

        $area = Area::findOne($area_id);

        if ($area->depth == 1) {
            $this->addError('area_id', '请选择城市！');

            return false;
        }

        if ($area->depth == 2) {
            $this->addError('area_id', '请选择区域！');

            return false;
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->is_default) {
            static::updateAll(['is_default' => 0], [
                'and',
                ['=', 'user_id', $this->user_id],
                ['<>', 'id', $this->id],
            ]);
        }
    }

    public function showAreaLine()
    {
        $areas = Area::getParentLine($this->area_id);
        foreach ($areas as &$area) {
            $area = Area::getNameById($area);
        }

        return join("", $areas);
    }
}
