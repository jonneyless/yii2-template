<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\StoreCategory */
/* @var $form yii\bootstrap\ActiveForm  */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?php if(!$this->context->store_id){ ?>
        <?= $form->field($model, 'store_id')->dropDownList($model->getStoreSelectData()) ?>
        <?php } ?>
        <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'status')->radioList($model->getStatusSelectData()) ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
