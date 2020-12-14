<?php

use common\widgets\ActiveField;
use wap\assets\PageAsset;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Group */
/* @var $order \common\models\Order */
/* @var $form yii\widgets\ActiveForm */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$event_name = '爱拼才会赢';
$this->title = $model->one_delivery == 1 ? '帮我拼' : '一起拼';
?>

<div class="box box-nobg">
    <div class="alert alert-success text-center" role="alert">
        <span class="glyphicon glyphicon-ok-sign"></span> 你的好友正在参加建行“<?= $event_name ?>”活动，助他一臂之力吧！<span class="red">活动详情请点击右下角“活动介绍”查看。</span>
    </div>
</div>

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

    <?php if ($step == 1) { ?>
        <?= $form->field($order, 'phone')->label('手机号码')->textInput(['maxlength' => true]) ?>

        <?= $form->field($order, 'vcode')->label('验证码')->mobileVcode(['mobileInputId' => Html::getInputId($order, 'phone')]) ?>
    <?php } ?>

    <?php if ($step == 2) { ?>
        <?= $form->field($order, 'consignee')->textInput() ?>

        <?= $form->field($order, 'phone')->textInput(['readonly' => true]) ?>

        <?= $form->field($order, 'area_id')->area() ?>

        <?= $form->field($order, 'address')->textInput(['maxlength' => true]) ?>

        <?= Html::activeHiddenInput($order, 'vcode') ?>
    <?php } ?>

    <div class="form-group">
        <div class="col-sm-12">
            <?= Html::hiddenInput('step', $step) ?>
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary form-control']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
