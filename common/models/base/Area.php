<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property string $area_id 地区 ID
 * @property string $parent_id 父级 ID
 * @property string $parent_arr 父级链
 * @property int $child 是否有子级
 * @property string $child_arr 子集合集
 * @property string $name 地区名称
 * @property int $depth 层级
 * @property string $initial 首字母
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property int $status 状态
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'child', 'depth', 'status'], 'integer'],
            [['child_arr'], 'string'],
            [['name'], 'required'],
            [['parent_arr'], 'string', 'max' => 255],
            [['name', 'longitude', 'latitude'], 'string', 'max' => 30],
            [['initial'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'area_id' => '地区 ID',
            'parent_id' => '父级 ID',
            'parent_arr' => '父级链',
            'child' => '是否有子级',
            'child_arr' => '子集合集',
            'name' => '地区名称',
            'depth' => '层级',
            'initial' => '首字母',
            'longitude' => '经度',
            'latitude' => '纬度',
            'status' => '状态',
        ];
    }
}
