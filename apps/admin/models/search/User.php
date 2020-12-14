<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\User as UserModel;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索模型
 *
 * {@inheritdoc}
 *
 * @property $referee
 * @property $mobile
 */
class User extends Model
{

    public $referee;
    public $mobile;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referee', 'mobile'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'referee' => '推荐人',
            'mobile' => '手机号',
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
        $query = UserModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'mobile' => $this->mobile,
        ]);

        $referee = UserModel::find()->where(['mobile' => $this->referee])->one();

        if($referee){
            $query->andFilterWhere([
                'referee' => $referee->user_id,
            ]);
        }

        return $dataProvider;
    }
}
