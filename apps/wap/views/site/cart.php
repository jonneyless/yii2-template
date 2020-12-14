<?php

use wap\assets\PageAsset;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model \common\models\Goods */
/* @var $order \common\models\Order */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$this->title = '立即拼单';
?>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['site/confirm']),
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
]);
?>

<div class="box">
    <div class="alert alert-warning" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign"></span> 订单有效期<?= Yii::$app->params['order.pay.expire']; ?>分钟，请在有效期内及时付款。
    </div>

    <div class="order-address">
        <?= $form->field($order, 'consignee')->textInput(['maxlength' => true]) ?>
        <?= $form->field($order, 'phone')->textInput(['maxlength' => true]) ?>
        <?= $form->field($order, 'area_id')->area() ?>
        <?= $form->field($order, 'address')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="box">
    <div class="order-item clearfix">
        <div class="order-item-preview">
            <img src="<?= $model->getPreview(200, 200) ?>"/>
        </div>

        <div class="order-item-info">
            <h4><?= $model->name ?></h4>
            <div class="pull-left black-gray">
                <br/>
                <br/>
                库存<strong><?= $model->stock ?></strong>件
            </div>

            <div class="pull-right text-right black-gray">
                价格：<span class="red">￥<?= $model->price ?></span><br/>
            </div>
        </div>
    </div>
</div>

<div class="box text-right black-gray">
    支付金额：<span class="red">￥<?= sprintf('%.2f', $model->price) ?></span>
</div>

<div class="box box-nobg">
    <?= $form->field($order, 'goods_id')->label(false)->hiddenInput() ?>
    <?= $form->field($order, 'quantity')->label(false)->hiddenInput() ?>

    <input id="to-confirm" type="button" class="btn btn-danger btn-lg form-control" value="提交订单"/>
</div>

<?php ActiveForm::end(); ?>
