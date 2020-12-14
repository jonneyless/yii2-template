<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '更新缓存', 'url' => ['cache'], 'options' => ['class' => 'btn btn-success']],
];
?>

<div class="ibox">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            'key',
            'name',
            [
                'attribute' => 'parent',
                'value' => function($data){
                    return $data->getParentName();
                },
            ],

            [
                'class' => 'ijony\admin\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'text-right',
                ],
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>