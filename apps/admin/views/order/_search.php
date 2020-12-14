<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model common\models\search\Order */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldClass' => ActiveField::className(),
    ]); ?>

    <?php echo $form->field($model, 'begin_time')->betweenDate('end_time', ['has_time' => false]) ?>

    <div class="form-group">
        <?php echo Html::radio('export', false, ['value' => '', 'id' => 'export']) ?> &nbsp;
        <label class="control-label" for="export">不导出</label>
        <?php echo Html::radio('export', false, ['value' => 'refund', 'id' => 'export_refund']) ?> &nbsp;
        <label class="control-label" for="export_refund">导出退款订单</label>
        <?php echo Html::radio('export', false, ['value' => 'delivery', 'id' => 'export_delivery']) ?> &nbsp;
        <label class="control-label" for="export_delivery">导出发货订单</label>
    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
