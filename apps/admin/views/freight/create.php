<?php

/* @var $this yii\web\View */
/* @var $model admin\models\StoreFreight */

$this->title = '添加运费模板';
$this->params['breadcrumbs'][] = ['label' => '运费模板管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>