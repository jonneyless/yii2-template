<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\Category as CategoryModel;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索模型
 *
 * {@inheritdoc}
 *
 * @property $parent_id
 * @property $name
 */
class Category extends Model
{

    public $parent_id;
    public $name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'parent_id' => '父级',
            'name'      => '名称',
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
        $query = CategoryModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
