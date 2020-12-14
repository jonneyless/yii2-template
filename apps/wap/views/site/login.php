<?php

use wap\assets\PageAsset;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\User */
/* @var $form yii\bootstrap\ActiveForm */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$this->title = '手机验证';
?>

<div class="box">
    <?php $form = ActiveForm::begin([
        'fieldClass' => \common\widgets\ActiveField::className(),
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

    <?= $form->field($model, 'mobile')->label(false)->textInput(['id' => 'mobile', 'placeholder' => '请输入手机号码', 'autocomplate' => 'off']) ?>
    <?= $form->field($model, 'vcode')->label(false)->mobileVcode(['mobileInputId' => 'mobile', 'placeholder' => '请输入验证码', 'autocomplate' => 'off']) ?>

    <div class="form-group">
        <div class="col-sm-12">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary form-control']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
