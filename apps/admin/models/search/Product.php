<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\Product as ProductModel;
use yii\data\ActiveDataProvider;

/**
 * 产品搜索模型
 *
 * {@inheritdoc}
 *
 * @property $id
 * @property $category
 * @property $name
 * @property $status
 */
class Product extends Model
{

    public $id;
    public $category;
    public $name;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category', 'name', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => '分类',
            'name' => '名称',
            'status' => '状态',
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
        $query = ProductModel::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
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

    public function getStatusData()
    {
        return [
            ProductModel::STATUS_UNACTIVE => '禁用',
            ProductModel::STATUS_ACTIVE => '激活',
        ];
    }
}
