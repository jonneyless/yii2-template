<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 *
 * @property \common\models\ProductGallery[] $gallery
 * @property \common\models\Category $category
 */
class Product extends \common\models\base\Product
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 商品组图
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasMany(ProductGallery::className(), ['product_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * 商品分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }
}
