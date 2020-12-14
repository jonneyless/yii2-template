<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\AdminAuth */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '权限列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ibox">
    <div class="ibox-content">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'key',
            'name',
            [
                'attribute' => 'parent',
                'value' => function($data){
                    return $data->getParentName();
                },
            ],
            'description',
            [
                'attribute' => 'route',
                'format' => 'html',
                'value' => function($data){
                    return $data->getRoute();
                },
            ],
        ],
    ]) ?>

    </div>
</div>
