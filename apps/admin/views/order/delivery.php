<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = '订单发货：' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '订单发货';
?>
<div class="event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="event-form">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
        ]); ?>

        <?= $form->field($model, 'delivery_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'delivery_number')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('确认发货', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
