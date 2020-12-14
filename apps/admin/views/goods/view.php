<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\Goods */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '货品', 'url' => ['goods/mode/index', 'id' => $model->goods_id], 'options' => ['class' => 'btn btn-warning']],
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
    ['label' => '编辑', 'url' => ['update', 'id' => $model->goods_id], 'options' => ['class' => 'btn btn-primary']],
    ['label' => '移除', 'url' => ['remove', 'id' => $model->goods_id], 'options' => [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => '确定要放入回收站吗？',
            'method' => 'post',
        ],
    ]],
];

if(
    Yii::$app->user->identity->store
    && Yii::$app->user->identity->store->pospal_app_id
    && Yii::$app->user->identity->store->pospal_app_key
    && $model->store_id == Yii::$app->user->identity->store->store_id
){
    $this->params['buttons'][] = ['label' => '推送', 'url' => ['push', 'id' => $model->goods_id], 'options' => ['class' => 'btn btn-default']];
}
?>

<div class="ibox">
    <div class="ibox-content">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'goods_id',
            'name',
            [
                'attribute' => 'preview',
                'format' => ['image', ['style' => 'max-height: 100px;']],
                'value' => function($data){
                    return $data->getPreview();
                },
            ],
            [
                'attribute' => 'category_id',
                'value' => function($data){
                    return $data->getCategoryName();
                },
            ],
            [
                'attribute' => 'store_id',
                'value' => function($data){
                    return $data->getStoreName();
                },
            ],
            [
                'attribute' => 'store_category_id',
                'value' => function($data){
                    return $data->getStoreCategoryName();
                },
            ],
            'number',
            'original_price',
            'member_price',
            [
                'attribute' => 'shelves_at',
                'format' => [
                    'datetime',
                    'yyyy-MM-dd'
                ],
            ],
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
</div>
