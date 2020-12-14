<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'goods_id',
                'value' => $model->goods->name,
            ],
            [
                'attribute' => 'group_id',
                'value' => '拼单 #' . $model->group_id,
            ],
            'price',
            'quantity',
            'amount',
            'paid',
            'consignee',
            'phone',
            'delivery_name',
            'delivery_number',
            [
                'attribute' => 'address',
                'value' => $model->showAreaLine() . " " . $model->address,
            ],
            [
                'attribute' => 'created_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd HH:mm:ss',
                ],
            ],
            [
                'attribute' => 'updated_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd HH:mm:ss'
                ],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => $model->showStatus(),
            ],
        ],
    ]) ?>

</div>
