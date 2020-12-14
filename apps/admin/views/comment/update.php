<?php

/* @var $this yii\web\View */
/* @var $model admin\models\Comment */

$this->title = '编辑评论：' . $model->goods->name;
$this->params['breadcrumbs'][] = ['label' => '评论管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->goods->name, 'url' => ['view', 'id' => $model->comment_id]];
$this->params['breadcrumbs'][] = '编辑';
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>