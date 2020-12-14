<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\Store */
/* @var $form yii\bootstrap\ActiveForm  */
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
            'layout' => 'horizontal',
            'options'=>[
                'enctype'=>'multipart/form-data',
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
        <?= $form->field($model, 'is_offline')->radioList(['否', '是']) ?>
        <?= $form->field($model, 'owner')->textInput(['maxlength' => true, 'value' => $model->getOwner('mobile')]) ?>
        <?= $form->field($model, 'referee')->textInput(['maxlength' => true, 'value' => $model->getReferee('mobile')]) ?>
        <?= $form->field($model, 'preview')->image() ?>
        <?= $form->field($model, 'pospal_app_id')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'pospal_app_key')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

        <div class="form-group field-store-address">
            <label class="control-label col-sm-2">&nbsp;</label>
            <div class="col-sm-10">
                <div id="map" style="height: 400px"></div>
                <?= Html::activeHiddenInput($model, 'longitude') ?>
                <?= Html::activeHiddenInput($model, 'latitude') ?>
            </div>
        </div>

        <?= $form->field($model, 'service_phone')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'service_qq')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'content')->editor() ?>
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

$longitudeInputId = Html::getInputId($model, 'longitude');
$latitudeInputId = Html::getInputId($model, 'latitude');
$longitude = $model->longitude ? $model->longitude : ($model->address ? 0 : 116.404);
$latitude = $model->latitude ? $model->latitude : ($model->address ? 0 : 39.915);
$address = $model->address;

$js = <<<JS

var lng = $longitude;
var lat = $latitude;
var addr = '$address';
var lngId = '$longitudeInputId';
var latId = '$latitudeInputId';
var map = new BMap.Map("map");
var point = new BMap.Point(lng, lat);
var marker = new BMap.Marker(point);
var myGeo = new BMap.Geocoder();

if(lng == 0 && addr != ''){
    myGeo.getPoint(addr, function(point){
        if(point){
            map.centerAndZoom(point, 15);
            marker = new BMap.Marker(point);
            map.addOverlay(marker);
        }else{
            alert("您选择地址没有解析到结果!");
        }
    });
}else{
    map.centerAndZoom(point, 15);
    map.addOverlay(marker);
}

map.enableScrollWheelZoom(true);
map.addEventListener("click", function(e){
    map.removeOverlay(marker);
    point = new BMap.Point(e.point.lng, e.point.lat);
    marker = new BMap.Marker(point);
    map.addOverlay(marker);
    
    $('#' + lngId).val(e.point.lng);
    $('#' + latId).val(e.point.lat);
});
JS;

$this->registerJs($js);
$this->registerJsFile('http://api.map.baidu.com/api?v=3.0&ak=gkcuOhQQTkPWmyKPcGeClcYbwxqMAzkd');