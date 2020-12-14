<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Order;
use common\models\Group;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\Order */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'goods_id',
                'value' => function ($data) {
                    return $data->goods->name;
                },
            ],
            [
                'attribute' => 'group_id',
                'value' => function ($data) {
                    return '拼单 #' . $data->group_id;
                },
            ],
            'phone',
            [
                'attribute' => 'created_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd HH:mm:ss',
                ],
            ],
            [
                'attribute' => 'is_first',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->is_first ? '<span class="black">发起</span>' : '<span class="gray">参与</span>';
                },
                'filter' => ['参与', '发起'],
            ],
            [
                'attribute' => 'is_virtual',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->goods->is_virtual ? '<span class="black">是</span>' : '<span class="gray">否</span>';
                },
                'filter' => ['否', '是'],
            ],
            [
                'attribute' => 'group_status',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->group->showStatus();
                },
                'filter' => Group::getStatusSelectData(),
            ],
            [
                'attribute' => 'payment_status',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->showPaymentStatus();
                },
                'filter' => Order::getPaymentStatusSelectData(),
            ],
            [
                'attribute' => 'delivery_status',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->showDeliveryStatus();
                },
                'filter' => Order::getDeliveryStatusSelectData(),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->showOrderStatus();
                },
                'filter' => Order::getStatusSelectData(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {refund} {delivery}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('查看', $url);
                    },
                    'refund' => function ($url, $model, $key) {
                        /* @var $model \common\models\Order */
                        if ($model->payment_status == Order::PAYMENT_REFUND) {
                            return "| " . Html::a('退款', $url);
                        }
                    },
                    'delivery' => function ($url, $model, $key) {
                        /* @var $model \common\models\Order */
                        if ($model->group->status == \common\models\Group::STATUS_OVER && $model->goods->is_virtual == 0 && $model->status == Order::STATUS_PAID && $model->delivery_status == Order::DELIVERY_NO &&
                            (($model->is_first == Order::IS_FIRST_YES && $model->goods->one_delivery == 1) || $model->goods->one_delivery == 0)) {
                            return "| " . Html::a('发货', $url);
                        }
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
