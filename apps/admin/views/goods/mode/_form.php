<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\Category */
/* @var $form yii\bootstrap\ActiveForm  */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
            'options'=>[
                'enctype'=>'multipart/form-data',
            ],
        ]); ?>

        <?php if(!$model->name){ ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?php } ?>
        <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'original_price')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'stock')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'image')->image() ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
