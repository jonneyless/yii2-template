<?php

/* @var $this yii\web\View */
/* @var $model admin\models\Category */

$this->title = '添加货品';
$this->params['breadcrumbs'][] = ['label' => $this->context->goods->name, 'url' => ['goods/view', 'id' => $this->context->goods->goods_id]];
$this->params['breadcrumbs'][] = ['label' => '货品管理', 'url' => ['index', 'id' => $this->context->goods->goods_id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>