<?php

use common\widgets\ActiveField;
use wap\assets\PageAsset;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Address */
/* @var $form yii\widgets\ActiveForm */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$this->title = '新建收货地址';
?>

<div class="box">
    <?php $form = ActiveForm::begin([
        'fieldClass' => ActiveField::className(),
        'layout' => 'horizontal',
        'enableClientScript' => false,
        'fieldConfig' => [
            'inline' => true,
            'horizontalCssClasses' => [
                'label' => 'col-sm-2',
                'offset' => 'col-sm-offset-2',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'consignee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'area_id')->area() ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_default')->checkbox(['value' => 1]) ?>

    <div class="form-group">
        <div class="col-sm-12">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary form-control']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
