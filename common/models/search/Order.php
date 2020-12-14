<?php

namespace common\models\search;

use common\models\base\Goods;
use common\models\Group;
use libs\Utils;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order as OrderModel;

/**
 * Order represents the model behind the search form about `common\models\Order`.
 *
 * @property integer $is_virtual
 * @property integer $group_status
 */
class Order extends OrderModel
{
    public $begin_time;
    public $end_time;
    public $is_virtual;
    public $group_status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'begin_time', 'end_time', 'phone'], 'safe'],
            [['group_id', 'quantity', 'payment_status', 'delivery_status', 'status', 'is_first', 'is_virtual', 'group_status'], 'integer'],
            [['amount', 'paid'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '订单 ID',
            'user_id' => '用户 ID',
            'goods_id' => '商品 ID',
            'group_id' => '拼单 ID',
            'price' => '单价',
            'quantity' => '数量',
            'amount' => '总金额',
            'paid' => '已付金额',
            'consignee' => '收货人',
            'area_id' => '地址区域',
            'address' => '收货地址',
            'phone' => '联系电话',
            'delivery_name' => '物流名称',
            'delivery_number' => '物流单号',
            'pay_card' => '支付卡号',
            'is_first' => '角色',
            'is_virtual' => '虚拟卡',
            'begin_time' => '下单时间',
            'group_status' => '拼单状态',
            'created_at' => '下单时间',
            'updated_at' => '更新时间',
            'payment_status' => '支付状态',
            'delivery_status' => '物流状态',
            'status' => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OrderModel::find()->alias('order');
        $query->joinWith(['goods as goods']);
        $query->joinWith(['group as group']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order.user_id' => $this->user_id,
            'order.group_id' => $this->group_id,
            'order.amount' => $this->amount,
            'order.paid' => $this->paid,
            'order.phone' => $this->phone,
            'order.created_at' => $this->created_at,
            'order.payment_status' => $this->payment_status,
            'order.delivery_status' => $this->delivery_status,
            'order.status' => $this->status,
            'order.is_first' => $this->is_first,
            'group.status' => $this->group_status,
            'goods.is_virtual' => $this->is_virtual,
        ]);

        $query->andFilterWhere(['like', 'order.id', $this->id])
            ->andFilterWhere(['like', 'goods.name', $this->goods_id]);

        if ($this->begin_time && $this->end_time) {
            $beginTime = strtotime($this->begin_time);
            $endTime = strtotime($this->end_time) + 3600 * 24;
            $query->andFilterWhere(['between', 'order.created_at', $beginTime, $endTime]);
        }

        return $dataProvider;
    }

    public function export($params, $type)
    {
        $this->load($params);

        return $this->{"export_" . $type}();
    }

    private function export_refund()
    {
        $query = OrderModel::find();

        $query->where(['payment_status' => OrderModel::PAYMENT_REFUND]);

        if ($this->begin_time && $this->end_time) {
            $beginTime = strtotime($this->begin_time);
            $endTime = strtotime($this->end_time) + 3600 * 24;
            $query->andFilterWhere(['between', 'created_at', $beginTime, $endTime]);
        }

        $query->orderBy(['goods_id' => SORT_ASC]);

        $datas = $query->all();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="退款订单数据.csv"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        $head = ['订单 ID', '商品 ID', '拼单 ID', '金额', '联系电话', '下单时间'];
        $empty = ['', '', '', '', ''];
        foreach ($head as $i => $v) {
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $head);

        $index = 0;
        $limit = 5000;

        /* @var $data \common\models\Order */
        foreach ($datas as $data) {
            $index++;

            if ($limit == $index) {
                ob_flush();
                flush();
                $index = 0;
            }

            $order = [];
            $order[] = iconv('utf-8', 'gbk', $data->id);
            $order[] = iconv('utf-8', 'gbk', $data->goods->name);
            $order[] = iconv('utf-8', 'gbk', '拼单 #' . $data->group_id);
            $order[] = iconv('utf-8', 'gbk', $data->paid);
            $order[] = iconv('utf-8', 'gbk', $data->phone);
            $order[] = iconv('utf-8', 'gbk', date('Y-m-d H:i:s', $data->created_at));

            fputcsv($fp, $order);
        }

        fclose($fp);
        die();
    }

    private function export_delivery()
    {
        $query = OrderModel::find()->alias('order')->joinWith(['goods as goods', 'group as group']);

        $query->where([
            'order.is_first' => OrderModel::IS_FIRST_YES,
            'goods.is_virtual' => 0,
            'group.status' => Group::STATUS_OVER,
            'order.payment_status' => OrderModel::PAYMENT_DONE,
            'order.delivery_status' => OrderModel::DELIVERY_NO,
            'order.status' => OrderModel::STATUS_PAID,
        ]);

        if ($this->begin_time && $this->end_time) {
            $beginTime = strtotime($this->begin_time);
            $endTime = strtotime($this->end_time) + 3600 * 24;
            $query->andFilterWhere(['between', 'order.created_at', $beginTime, $endTime]);
        }

        $query->orderBy(['goods_id' => SORT_ASC]);

        $datas = $query->all();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="发货订单数据.csv"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        $head = ['订单 ID', '商品 ID', '拼单 ID', '联系人', '联系电话', '收货地址', '下单时间'];
        $empty = ['', '', '', '', ''];
        foreach ($head as $i => $v) {
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $head);

        $index = 0;
        $limit = 5000;

        /* @var $data \common\models\Order */
        foreach ($datas as $data) {
            $index++;

            if ($limit == $index) {
                ob_flush();
                flush();
                $index = 0;
            }

            $order = [];
            $order[] = iconv('utf-8', 'gbk', $data->id);
            $order[] = iconv('utf-8', 'gbk', $data->goods->name);
            $order[] = iconv('utf-8', 'gbk', '拼单 #' . $data->group_id);
            $order[] = iconv('utf-8', 'gbk', $data->consignee);
            $order[] = iconv('utf-8', 'gbk', $data->phone);
            $order[] = iconv('utf-8', 'gbk', $data->showAreaLine() . ' ' . $data->address);
            $order[] = iconv('utf-8', 'gbk', date('Y-m-d H:i:s', $data->created_at));

            fputcsv($fp, $order);
        }

        fclose($fp);
        die();
    }
}
