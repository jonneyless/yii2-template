<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '广告管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
];
?>

<div class="ibox">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            [
                'attribute' => 'ad_id',
                'header' => '#',
            ],
            'name',
            [
                'attribute' => 'image',
                'format' => ['image', ['style' => 'max-width: 200px; max-height: 60px;']],
                'value' => function($data){
                    return $data->getImage();
                },
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getTypeLabel();
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getStatusLabel();
                },
            ],

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
            ],
        ],
    ]); ?>
</div>
