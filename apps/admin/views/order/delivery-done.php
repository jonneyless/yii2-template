<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\Order */

$this->title = '发货处理：订单号 ' . $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '发货管理', 'url' => ['delivery']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['buttons'] = [
    ['label' => '管理', 'url' => ['delivery'], 'options' => ['class' => 'btn btn-info']],
];
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'delivery_type')->dropDownList(Yii::$app->params['delivery_type']) ?>
        <?= $form->field($model, 'delivery_number')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>