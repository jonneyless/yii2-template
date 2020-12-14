<?php

use ijony\admin\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data \admin\models\Goods */

$this->title = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
    ['label' => '审核', 'url' => ['approval'], 'options' => ['class' => 'btn btn-danger']],
    ['label' => '导出', 'url' => ['export'], 'options' => ['class' => 'btn btn-info']],
    ['label' => '回收站', 'url' => ['recycle'], 'options' => ['class' => 'btn btn-default']],
];

$buttons = [
    'mode' => function ($url, $model, $key) {
        $options = [
            'title' => '货品',
            'class' => 'btn-white btn btn-xs',
        ];
        return \yii\helpers\Html::a('货品', ['goods/mode/index', 'id' => $key], $options);
    },
    'comment' => function ($url, $model, $key) {
        $options = [
            'title' => '加评论',
            'class' => 'btn-white btn btn-xs',
        ];
        return \yii\helpers\Html::a('加评论', ['comment/create', 'goods_id' => $key], $options);
    },
];

if(Yii::$app->user->identity->store && Yii::$app->user->identity->store->pospal_app_id && Yii::$app->user->identity->store->pospal_app_key){
    $buttons['push'] = function ($url, $model, $key){
        $options = [
            'title' => '推送',
            'class' => 'btn-white btn btn-xs',
        ];
        return \yii\helpers\Html::a('推送', ['goods/push', 'id' => $key], $options);
    };
}
?>

<div class="ibox">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layoutFix' => true,
        'columns' => [
            [
                'attribute' => 'goods_id',
                'header' => '#',
            ],
            [
                'attribute' => 'preview',
                'value' => function($data){
                    return $data->getPreview(200, 200);
                },
                'format' => ['image', ['style' => 'max-width: 200px; max-height: 50px;']],
            ],
            [
                'attribute' => 'category_id',
                'value' => function($data){
                    return $data->category ? $data->category->name : '';
                },
            ],
            'name',
            [
                'attribute' => 'is_hot',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getIsHotLabel();
                },
            ],
            [
                'attribute' => 'is_recommend',
                'format' => 'raw',
                'value' => function($data){
                    return $data->getIsRecommendLabel();
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
                'template' => '{view} {mode} {update} {remove} {comment}',
                'buttons' => $buttons
            ],
        ],
    ]); ?>
</div>

