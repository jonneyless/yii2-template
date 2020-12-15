<?php

use yii\helpers\Html;
use ijony\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\Admin */
/* @var $form ijony\admin\widgets\ActiveForm */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
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

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->hint($model->getIsNewRecord() ? false : '不修改密码请留空') ?>
        <?= $form->field($model, 'role_id')->dropDownList($model->getRoleSelectData(), ['prompt' => '请选择']) ?>
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
