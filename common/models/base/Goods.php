<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property string $goods_id ID
 * @property string $product_id 产品 ID
 * @property string $pospal_id 银豹 ID
 * @property string $category_id 分类 ID
 * @property string $store_id 店铺 ID
 * @property string $store_category_id 店铺分类 ID
 * @property string $name 名称
 * @property string $preview 主图
 * @property string $number 编号
 * @property string $bar_code 条形码
 * @property string $original_price 商超价
 * @property string $member_price 会员价
 * @property string $cost_price 成本价
 * @property string $content 详情
 * @property string $weight 重量
 * @property int $created_at 添加时间
 * @property int $updated_at 修改时间
 * @property int $shelves_at 上架时间
 * @property int $free_express 包邮件数
 * @property int $is_hot 热销
 * @property int $is_recommend 推荐
 * @property string $goods_score 商品评分
 * @property string $store_score 店铺评分
 * @property string $delivery_score 物流评分
 * @property int $status 状态
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'category_id', 'store_id', 'store_category_id', 'created_at', 'updated_at', 'shelves_at', 'free_express', 'is_hot', 'is_recommend', 'status'], 'integer'],
            [['category_id', 'store_id', 'store_category_id', 'name', 'preview', 'number'], 'required'],
            [['original_price', 'member_price', 'cost_price', 'weight', 'goods_score', 'store_score', 'delivery_score'], 'number'],
            [['content'], 'string'],
            [['pospal_id'], 'string', 'max' => 60],
            [['name'], 'string', 'max' => 100],
            [['preview'], 'string', 'max' => 150],
            [['number'], 'string', 'max' => 30],
            [['bar_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'ID',
            'product_id' => '产品 ID',
            'pospal_id' => '银豹 ID',
            'category_id' => '分类 ID',
            'store_id' => '店铺 ID',
            'store_category_id' => '店铺分类 ID',
            'name' => '名称',
            'preview' => '主图',
            'number' => '编号',
            'bar_code' => '条形码',
            'original_price' => '商超价',
            'member_price' => '会员价',
            'cost_price' => '成本价',
            'content' => '详情',
            'weight' => '重量',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'shelves_at' => '上架时间',
            'free_express' => '包邮件数',
            'is_hot' => '热销',
            'is_recommend' => '推荐',
            'goods_score' => '商品评分',
            'store_score' => '店铺评分',
            'delivery_score' => '物流评分',
            'status' => '状态',
        ];
    }
}
