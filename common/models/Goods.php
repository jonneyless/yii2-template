<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * 商品数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\GoodsAttribute[] $attr
 * @property \common\models\GoodsGallery[] $gallery
 * @property \common\models\GoodsInfo $info
 * @property \common\models\Category $category
 * @property \common\models\Store $store
 * @property \common\models\StoreCategory $storeCategory
 */
class Goods extends namespace\base\Goods
{

    const FREE_EXPRESS_NO = 0;
    const FREE_EXPRESS_YES = 9;

    const IS_HOT_UNACTIVE = 0;
    const IS_HOT_ACTIVE = 9;

    const IS_RECOMMEND_UNACTIVE = 0;
    const IS_RECOMMEND_ACTIVE = 9;

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_OFFLINE = 2;  // 线下
    const STATUS_ACTIVE = 9;    // 启用

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'store_id', 'store_category_id', 'name', 'number'], 'required'],
            [['category_id', 'store_id', 'store_category_id', 'created_at', 'updated_at', 'shelves_at', 'free_express', 'is_hot', 'is_recommend', 'status'], 'integer'],
            [['original_price', 'member_price', 'cost_price', 'weight', 'goods_score', 'store_score', 'delivery_score'], 'number'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['number'], 'string', 'max' => 30],
            [['bar_code'], 'string', 'max' => 255],
        ];
    }

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
     * 商品属性
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttr()
    {
        return $this->hasMany(GoodsAttribute::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 商品组图
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasMany(GoodsGallery::className(), ['goods_id' => 'goods_id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * 商品附加信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInfo()
    {
        return $this->hasOne(GoodsInfo::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 商品分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }

    /**
     * 店铺信息
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }

    /**
     * 店铺商品分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getStoreCategory()
    {
        return $this->hasOne(StoreCategory::className(), ['category_id' => 'store_category_id']);
    }

    public function checkStore($store_id)
    {
        if(!$store_id){
            return true;
        }

        return $this->store_id == $store_id;
    }
}
