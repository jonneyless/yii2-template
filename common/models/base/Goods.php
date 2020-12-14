<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property string $id
 * @property string $category_id
 * @property string $name
 * @property string $sub_name
 * @property string $preview
 * @property string $stock
 * @property string $sales
 * @property string $price
 * @property string $description
 * @property string $content
 * @property integer $status
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'stock', 'sales', 'status'], 'integer'],
            [['category_id', 'name', 'preview'], 'required'],
            [['price'], 'number'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 150],
            [['sub_name', 'description'], 'string', 'max' => 255],
            [['preview'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品 ID',
            'category_id' => '分类 ID',
            'name' => '商品名称',
            'sub_name' => '商品提示',
            'preview' => '预览图',
            'stock' => '库存',
            'sales' => '销量',
            'price' => '单价',
            'description' => '商品简介',
            'content' => '商品详情',
            'status' => '状态',
        ];
    }
}
