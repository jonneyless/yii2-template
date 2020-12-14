<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\Goods as GoodsModel;
use yii\data\ActiveDataProvider;

/**
 * 商品搜索模型
 *
 * {@inheritdoc}
 *
 * @property $id
 * @property $store_id
 * @property $category
 * @property $name
 * @property $number
 * @property $is_hot
 * @property $is_recommend
 * @property $status
 * @property $bar_code
 */
class Goods extends Model
{

    public $id;
    public $store_id;
    public $category;
    public $name;
    public $number;
    public $is_hot;
    public $is_recommend;
    public $status;
    public $bar_code;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'store_id', 'category', 'name', 'number', 'is_hot', 'is_recommend', 'status', 'bar_code'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '店铺 ID',
            'category' => '分类',
            'name' => '名称',
            'number' => '编号',
            'is_hot' => '热销',
            'is_recommend' => '推荐',
            'status' => '状态',
            'bar_code' => '条形码',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $store_id)
    {
        $query = GoodsModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if($store_id){
            $this->store_id = $store_id;
        }

        $query->andFilterWhere([
            'goods_id' => $this->id,
            'store_id' => $this->store_id,
            'number' => $this->number,
            'is_hot' => $this->is_hot,
            'is_recommend' => $this->is_recommend,
            'status' => $this->status,
            'bar_code' => $this->bar_code,
        ]);

        if($this->category){
            $category = \admin\models\Category::find()->where(['name' => $this->category])->one();
            $query->andFilterWhere([
                'category_id' => $category->getChildrenIds(),
            ]);
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public static function getIsHotData()
    {
        return [
            \admin\models\Goods::IS_HOT_UNACTIVE => '否',
            \admin\models\Goods::IS_HOT_ACTIVE => '是',
        ];
    }

    public function getIsRecommendData()
    {
        return [
            \admin\models\Goods::IS_RECOMMEND_UNACTIVE => '否',
            \admin\models\Goods::IS_RECOMMEND_ACTIVE => '是',
        ];
    }

    public function getStatusData()
    {
        return [
            \admin\models\Goods::STATUS_UNACTIVE => '下架',
            \admin\models\Goods::STATUS_ACTIVE => '上架',
        ];
    }
}
