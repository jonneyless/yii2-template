<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\Teacher */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '老师管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'][] = ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']];
$this->params['buttons'][] = ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']];
$this->params['buttons'][] = ['label' => '编辑', 'url' => ['update', 'id' => $model->teacher_id], 'options' => ['class' => 'btn btn-primary']];
$this->params['buttons'][] = [
    'label' => '移除', 'url' => ['remove', 'id' => $model->teacher_id], 'options' => [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => '确定要放入回收站吗？',
            'method' => 'post',
        ],
    ],
];
?>

<div class="ibox">
    <div class="ibox-content">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'teacher_id',
                'store.name',
                'name',
                [
                    'attribute' => 'avatar',
                    'format' => ['image', ['style' => 'max-width: 400px; max-height: 200px;']],
                    'value' => $model->getAvatar(),
                ],
                'title',
                'intro:html',
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
