<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\Menu */
/* @var $form yii\bootstrap\ActiveForm  */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inline' => true,
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-sm-10',
                    'error' => '',
                    'hint' => '',
                ],
            ],
        ]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'parent_id')->select() ?>
        <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'controller')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'params')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'auth_item')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'status')->radioList($model->getStatusSelectData()) ?>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2">
                <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
