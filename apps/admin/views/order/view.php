<?php

use ijony\admin\grid\GridView;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\models\Order */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'][] = ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']];

if($model->isPaid()){
    $this->params['buttons'][] = ['label' => '发货', 'url' => ['delivery-done', 'id' => $model->order_id], 'options' => ['class' => 'btn btn-danger']];
}
?>

<div class="ibox">
    <div class="ibox-content m-b-sm border-bottom">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_id',
            'amount',
            'fee',
            'consignee',
            'address',
            'phone',
            [
                'attribute' => 'created_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd HH:mm:ss'
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
                'value' => $model->getStatus(),
            ],
        ],
    ]) ?>

    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            [
                'attribute' => 'preview',
                'format' => ['image', ['style' => 'max-width: 200px; max-height: 50px;']],
                'value' => function($data){
                    return \ijony\helpers\Image::getImg($data->preview);
                },
            ],
            'name',
            'quantity',
            'amount',
            [
                'attribute' => 'goods.weight',
                'value' => function($data){
                    return $data->goods->showWeight();
                },
            ],
            [
                'attribute' => 'attrs',
                'value' => function($data){
                    return $data->showAttrs();
                },
            ],
            'mode',

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        $url = Url::to(['goods/view', 'id' => $model->goods_id]);
                        return Html::a('相关商品', $url, ['class' => 'btn-white btn btn-xs', 'target' => '_blank']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
