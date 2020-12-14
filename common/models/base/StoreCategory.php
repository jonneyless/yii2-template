<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%store_category}}".
 *
 * @property string $category_id ID
 * @property string $store_id 店铺 ID
 * @property string $parent_id 父级分类
 * @property string $name 名称
 * @property int $child 是否有子级
 * @property string $parent_arr 父级链
 * @property string $child_arr 子级群
 * @property string $sort 排序
 * @property int $status 状态
 */
class StoreCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%store_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'name'], 'required'],
            [['store_id', 'parent_id', 'child', 'sort', 'status'], 'integer'],
            [['child_arr'], 'string'],
            [['name'], 'string', 'max' => 60],
            [['parent_arr'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'ID',
            'store_id' => '店铺 ID',
            'parent_id' => '父级分类',
            'name' => '名称',
            'child' => '是否有子级',
            'parent_arr' => '父级链',
            'child_arr' => '子级群',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
