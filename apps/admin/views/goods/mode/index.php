<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '货品管理';
$this->params['breadcrumbs'][] = ['label' => $this->context->goods->name, 'url' => ['goods/view', 'id' => $this->context->goods->goods_id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '新增', 'url' => ['create', 'id' => $this->context->goods->goods_id], 'options' => ['class' => 'btn btn-success']],
];
?>

<div class="ibox">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            [
                'attribute' => 'image',
                'format' => ['image', ['style' => 'max-width: 100px; max-height: 30px;']],
                'value' => function($data){
                    return $data->getImage();
                },
            ],
            'name',
            'value',
            'price',
            'stock',

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
            ],
        ],
    ]); ?>
</div>
