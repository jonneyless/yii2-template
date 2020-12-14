<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\Admin */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => '管理员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
    ['label' => '新增', 'url' => ['create'], 'options' => ['class' => 'btn btn-success']],
    ['label' => '编辑', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'btn btn-primary']],
    ['label' => '移除', 'url' => ['remove', 'id' => $model->id], 'options' => [
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
            'id',
            [
                'attribute' => 'role_id',
                'value' => function($data){
                    return $data->getRoleName();
                },
            ],
            [
                'attribute' => 'store_id',
                'value' => function($data){
                    return $data->getStoreName();
                },
            ],
            'username',
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
                'format' => 'raw',
                'value' => $model->getStatusLabel(),
            ]
        ],
    ]) ?>

    </div>
</div>
