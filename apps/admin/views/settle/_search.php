<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ActiveField;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ibox-content m-b-sm border-bottom">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldClass' => ActiveField::className(),
    ]); ?>

    <div class="row">

        <div class="col-sm-2">
            <div class="form-group">
                <label>月份</label>
                <?php echo Html::dropDownList('date', $date, $dates, ['class' => 'form-control']) ?>
            </div>
        </div>

        <div class="col-sm-1">
            <label>&nbsp;</label>
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary form-control']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
</div>
