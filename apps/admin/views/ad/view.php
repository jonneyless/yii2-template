<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\Ad */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
    ['label' => '编辑', 'url' => ['update', 'id' => $model->ad_id], 'options' => ['class' => 'btn btn-primary']],
    ['label' => '移除', 'url' => ['remove', 'id' => $model->ad_id], 'options' => [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => '确定要放入回收站吗？',
            'method' => 'post',
        ],
    ]],
];
?>

<div class="ibox">
    <div class="ibox-content">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ad_id',
            [
                'attribute' => 'type',
                'value' => $model->getType(),
            ],
            'name',
            [
                'attribute' => 'image',
                'format' => ['image', ['style' => 'max-width: 200px; max-height: 60px;']],
                'value' => function($data){
                    return $data->getImage();
                },
            ],
            'url',
            [
                'attribute' => 'status',
                'value' => $model->getStatus(),
            ],
        ],
    ]) ?>

    </div>
</div>
