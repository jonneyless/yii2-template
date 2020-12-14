<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->getIsNewRecord()) { ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
    <?php } ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

    <?php if ($model->id != 1 && $model->id != Yii::$app->user->id) { ?>
        <?= $form->field($model, 'status')->radioList([
            \common\models\Admin::STATUS_DELETED => '禁用',
            \common\models\Admin::STATUS_ACTIVE => '启用',
        ]) ?>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
