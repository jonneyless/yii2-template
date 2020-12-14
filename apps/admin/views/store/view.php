<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\Store */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '店铺管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(!$this->context->store_id){
    $this->params['buttons'][] =  ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']];
    $this->params['buttons'][] =  ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']];
}
$this->params['buttons'][] =  ['label' => '编辑', 'url' => ['update', 'id' => $model->store_id], 'options' => ['class' => 'btn btn-primary']];
if(!$this->context->store_id){
    $this->params['buttons'][] =  ['label' => '移除', 'url' => ['remove', 'id' => $model->store_id], 'options' => [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => '确定要放入回收站吗？',
            'method' => 'post',
        ],
    ]];
}
?>

<div class="ibox">
    <div class="ibox-content">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'store_id',
            'name',
            [
                'attribute' => 'preview',
                'format' => ['image', ['style' => 'max-width: 400px; max-height: 200px;']],
                'value' => $model->getPreview(),
            ],
            'content:html',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'yyyy-MM-dd HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', 'yyyy-MM-dd HH:mm:ss'],
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatus(),
            ],
        ],
    ]) ?>

    </div>
</div>
