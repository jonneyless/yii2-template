<?php

/* @var $this yii\web\View */
/* @var $model admin\models\form\Goods */

$this->title = '编辑商品：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
if($model->goods->status == \admin\models\Goods::STATUS_OFFLINE){
    $this->params['breadcrumbs'][] = ['label' => '商品审核', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->goods_id]];
$this->params['breadcrumbs'][] = '编辑';

if($model->goods->status == \admin\models\Goods::STATUS_OFFLINE){
    $this->params['buttons'] = [
        ['label' => '审核', 'url' => ['approval'], 'options' => ['class' => 'btn btn-info']],
    ];
}else{
    $this->params['buttons'] = [
        ['label' => '管理', 'url' => ['index'], 'options' => ['class' => 'btn btn-info']],
    ];
}
?>

<?php if($model->goods->store && $model->goods->store->is_offline){ ?>
<?= $this->render('_offline_form', [
    'model' => $model,
]) ?>
<?php }else{ ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
<?php } ?>