<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\Coupon as CouponModel;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索模型
 *
 * {@inheritdoc}
 *
 * @property $code
 * @property $mobile
 * @property $status
 */
class Coupon extends Model
{

    public $code;
    public $mobile;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'mobile', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => '券码',
            'mobile'=> '手机号码',
            'status'=> '状态',
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
        $query = CouponModel::find()->alias('coupon');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'coupon.code', $this->code]);

        if($this->mobile){
            $query->joinWith('user');
            $query->andFilterWhere(['like', 'user.mobile', $this->mobile]);
        }

        if($this->status !== null){
            if($this->status == 0) {
                $query->andFilterWhere(['=', 'coupon.user_id', 0]);
            }

            if($this->status > 0) {
                $query->andFilterWhere(['>', 'coupon.user_id', 0]);
            }
        }

        return $dataProvider;
    }
}
