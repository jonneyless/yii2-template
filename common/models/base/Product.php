<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property string $id 产品 ID
 * @property string $category_id 分类 ID
 * @property string $name 名称
 * @property string $preview 主图
 * @property string $bar_code 条形码
 * @property string $content 详情
 * @property string $weight 重量
 * @property int $created_at 添加时间
 * @property int $updated_at 修改时间
 * @property int $status 状态
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'bar_code'], 'required'],
            [['category_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['content'], 'string'],
            [['weight'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['bar_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '产品 ID',
            'category_id' => '分类 ID',
            'name' => '名称',
            'preview' => '主图',
            'bar_code' => '条形码',
            'content' => '详情',
            'weight' => '重量',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'status' => '状态',
        ];
    }
}
