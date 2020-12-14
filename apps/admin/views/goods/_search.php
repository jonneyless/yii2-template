<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\search\Goods */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ibox-content m-b-sm border-bottom">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'fieldClass' => ActiveField::className(),
    ]); ?>

    <div class="row">

        <div class="col-sm-2">
            <?php echo $form->field($model, 'id')->textInput() ?>
        </div>

        <?php if(!$this->context->store_id){ ?>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'store_id')->textInput() ?>
        </div>
        <?php } ?>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'number')->textInput() ?>
        </div>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'category')->textInput() ?>
        </div>

        <div class="col-sm-1">
            <label>&nbsp;</label>
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2">
            <?php echo $form->field($model, 'name')->textInput() ?>
        </div>

        <?php if($model->status != \admin\models\Goods::STATUS_DELETE){ ?>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_hot')->dropDownList($model::getIsHotData(), ['prompt' => '请选择']) ?>
        </div>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_recommend')->dropDownList($model::getIsRecommendData(), ['prompt' => '请选择']) ?>
        </div>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'status')->dropDownList($model::getStatusData(), ['prompt' => '请选择']) ?>
        </div>
        <?php } ?>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'bar_code')->textInput() ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
</div>
