<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property string $id
 * @property string $parent_id
 * @property string $parent_arr
 * @property integer $child
 * @property string $child_arr
 * @property string $name
 * @property integer $depth
 * @property string $initial
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%area}}';
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '地区 ID',
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
