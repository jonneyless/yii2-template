<?php

/* @var $this yii\web\View */
/* @var $model admin\models\StoreFreight */

$this->title = '编辑运费模板：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '运费模板管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->freight_id]];
$this->params['breadcrumbs'][] = '编辑';
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>