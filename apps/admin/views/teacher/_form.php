<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model admin\models\Teacher */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <div class="ibox">
        <div class="ibox-content">

            <?php $form = ActiveForm::begin([
                'fieldClass' => ActiveField::className(),
                'layout' => 'horizontal',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
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

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'store_id', [
                'template' => <<<HTML
{label}
<div class="col-sm-10">
    <div class="row">
        <div class="col-sm-6">{input}</div>
        <div class="col-sm-6">
            <div class="input-group">
                <input id="filterKey" type="text" class="form-control" placeholder="输入关键字筛选店铺...">
                <span class="input-group-btn">
                    <button id="filterStore" class="btn btn-default" type="button">确定</button>
                </span>
            </div>
        </div>
    </div>
{hint}
{error}
</div>
HTML
    ,
            ])->dropDownList($model->getStoreSelectData(), ['prompt' => '请选择']) ?>
            <?= $form->field($model, 'avatar')->image() ?>
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'intro')->editor() ?>
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

<?php

$csrfToken = Yii::$app->request->getCsrfToken();
$csrfName = Yii::$app->request->csrfParam;
$storeDropDown = Html::getInputId($model, 'store_id');
$storeDataUrl = Url::to(['ajax/filter-store']);

$js = <<<JS

$('#filterStore').click(function(){
    var keyword = $('#filterKey').val();
    
    $('#$storeDropDown option:first').nextAll().remove();
    
    if(keyword){
        $.post('$storeDataUrl', {'$csrfName': '$csrfToken', 'keyword': keyword}, function(data){
            if(data.html){
                $('#$storeDropDown').html(data.html);
            }
        }, 'json');
    }
});

JS;

$this->registerJs($js);