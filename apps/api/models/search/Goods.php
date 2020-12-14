<?php

namespace api\models\search;

use api\models\Category;
use Yii;
use yii\base\Model;
use api\models\Goods as GoodsModel;
use yii\data\ActiveDataProvider;

/**
 * 商品搜索模型
 *
 * {@inheritdoc}
 *
 * @property $category_id
 * @property $store_id
 * @property $store_category_id
 * @property $keyword
 * @property $is_hot
 * @property $is_recommend
 */
class Goods extends Model
{

    public $category_id;
    public $store_id;
    public $store_category_id;
    public $keyword;
    public $is_hot;
    public $is_recommend;
    public $sell;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'store_id', 'store_category_id', 'keyword', 'is_hot', 'is_recommend', 'sell'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GoodsModel::find()->joinWith('info as info');

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
            'sort'       => [
                'params'          => $params,
                'enableMultiSort' => true,
                'attributes'      => [
                    'created_at',
                    'updated_at',
                    'shelves_at',
                    'member_price',
                    'sell' => [
                        'asc'  => ['info.sell' => SORT_ASC],
                        'desc' => ['info.sell' => SORT_DESC],
                    ],
                ],
                'defaultOrder'    => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params, '');

        if($this->is_hot > 0){
            $this->is_hot = GoodsModel::IS_HOT_ACTIVE;
        }else{
            $this->is_hot = null;
        }

        if($this->is_recommend > 0){
            $this->is_recommend = GoodsModel::IS_RECOMMEND_ACTIVE;
        }else{
            $this->is_recommend = null;
        }

        if(!$this->validate()){
            return $dataProvider;
        }

        if($this->category_id){
            if($category = Category::findOne($this->category_id)){
                $this->category_id = explode(",", $category->child_arr);
            }
        }

//        if(!$this->store_id){
//            $this->store_id = \api\models\Store::find()->select('store_id')->where(['is_offline' => \api\models\Store::IS_OFFLINE_NO])->column();
//        }

        $query->andFilterWhere([
            'category_id'       => $this->category_id,
            'store_id'          => $this->store_id,
            'store_category_id' => $this->store_category_id,
            'is_hot'            => $this->is_hot,
            'is_recommend'      => $this->is_recommend,
            'status'            => GoodsModel::STATUS_ACTIVE,
        ]);

        $query->andFilterWhere(['like', 'name', $this->keyword]);

        return $dataProvider;
    }
}
