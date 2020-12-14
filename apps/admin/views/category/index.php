<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分类管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
];
?>

<div class="ibox">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            [
                'attribute' => 'category_id',
                'header' => '#',
            ],
            [
                'attribute' => 'parent_id',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getParentButton();
                },
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getChildButton();
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
