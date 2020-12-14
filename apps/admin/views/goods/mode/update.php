<?php

/* @var $this yii\web\View */
/* @var $model admin\models\GoodsMode */

$this->title = '编辑货品：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $this->context->goods->name, 'url' => ['goods/view', 'id' => $this->context->goods->goods_id]];
$this->params['breadcrumbs'][] = ['label' => '货品管理', 'url' => ['index', 'id' => $this->context->goods->goods_id]];
$this->params['breadcrumbs'][] = ['label' => $model->value, 'url' => ['view', 'goods_id' => $model->goods_id, 'value' => $model->value]];
$this->params['breadcrumbs'][] = '编辑';
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>