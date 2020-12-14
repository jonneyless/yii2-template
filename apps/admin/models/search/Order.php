<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use admin\models\Order as OrderModel;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索模型
 *
 * {@inheritdoc}
 *
 * @property $order_id
 * @property $status
 */
class Order extends Model
{

    public $order_id;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单号',
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

    public function search($params, $store_id)
    {
        $query = OrderModel::find();

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
            $query->andFilterWhere([
                'store_id' => $store_id,
            ]);
        }

        $query->andFilterWhere([
            'order_id' => $this->order_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

    public static function getStatusData()
    {
        return [
            OrderModel::STATUS_CANCEL => '已取消',
            OrderModel::STATUS_NEW => '待付款',
            OrderModel::STATUS_PAID => '已付款',
            OrderModel::STATUS_REFUND => '待退款',
            OrderModel::STATUS_DELIVERY => '已发货',
            OrderModel::STATUS_DONE => '已完成',
        ];
    }
}
