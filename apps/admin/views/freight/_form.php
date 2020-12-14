<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\StoreFreight */
/* @var $form yii\bootstrap\ActiveForm  */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
        ]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?php if(!$this->context->store_id){ ?>
        <?= $form->field($model, 'store_id')->dropDownList($model->getStoreSelectData()) ?>
        <?php } ?>
        <?= $form->field($model, 'fee')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'free')->textInput(['maxlength' => true]) ?>

        <div class="form-group field-games-pre_prize">
            <label class="control-label col-sm-3" for="games-pre_urls"><?= $model->getAttributeLabel('area_config') ?></label>
            <div class="col-sm-6">
                <?= Html::activeHiddenInput($model, 'area_config'); ?>
                <?php
                $inputName = Html::getInputName($model, 'format_area_config');
                ?>
                <div class="form-group row">
                    <div class="col-lg-3">省份</div>
                    <div class="col-lg-5">运费</div>
                    <div class="col-lg-3">包邮额度</div>
                </div>

                <?php if(isset($model->format_area_config['area_id'])){ ?>
                <?php foreach($model->format_area_config['area_id'] as $index => $area_id){ ?>
                <div class="form-group row">
                    <div class="col-lg-3"><?= Html::dropDownList($inputName . '[area_id][]', $area_id, $model->getProvinceSelectData(), ['class' => 'form-control', 'prompt' => '请选择']) ?></div>
                    <div class="col-lg-5"><?= Html::textInput($inputName . '[fee][]', $model->format_area_config['fee'][$index], ['class' => 'form-control']) ?></div>
                    <div class="col-lg-3"><?= Html::textInput($inputName . '[free][]', $model->format_area_config['free'][$index], ['class' => 'form-control']) ?></div>
                    <div class="col-lg-1"><a href="javascript:;" class="btn btn-default group-input-minus"><i class="fa fa-minus"></i></a></div>
                </div>
                <?php } ?>
                <?php } ?>

                <div class="form-group row">
                    <div class="col-lg-3"><?= Html::dropDownList($inputName . '[area_id][]', null, $model->getProvinceSelectData(), ['class' => 'form-control', 'prompt' => '请选择']) ?></div>
                    <div class="col-lg-5"><?= Html::textInput($inputName . '[fee][]', '', ['class' => 'form-control']) ?></div>
                    <div class="col-lg-3"><?= Html::textInput($inputName . '[free][]', '', ['class' => 'form-control']) ?></div>
                    <div class="col-lg-1"><a href="javascript:;" class="btn btn-default group-input-plus"><i class="fa fa-plus"></i></a></div>
                </div>
            </div>
            <p class="help-block help-block-error"></p>
        </div>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php

$js = <<<JS


    $(document).on('click', '.group-input-plus', function(){
        var selfRow = $(this).closest('.form-group');
        var inputRow = selfRow.clone();
        
        inputRow.find('.group-input-plus').children('i').removeClass('fa-plus').addClass('fa-minus');
        inputRow.find('.group-input-plus').addClass('group-input-minus').removeClass('group-input-plus');
        
        selfRow.before(inputRow);
        selfRow.find('select option:first').prop('selected', true);
        selfRow.find('input').val('');
    });

    $(document).on('click', '.group-input-minus', function(){
        $(this).closest('.form-group').remove();
    });
    
JS;

$this->registerJs($js);

