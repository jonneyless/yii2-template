<?php

use admin\assets\PageAsset;
use yii\jui\JuiAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\form\Product */
/* @var $form yii\bootstrap\ActiveForm  */

PageAsset::register($this)->init([
    'js' => [
        'js/laytpl/laytpl.js',
        'js/jquery.uploadifive.js',
    ],
]);

JuiAsset::register($this);
?>

<?php $form = ActiveForm::begin([
    'fieldClass' => ActiveField::className(),
    'layout' => 'horizontal',
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'tabs-container',
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

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab-base">基本</a></li>
    <li class=""><a data-toggle="tab" href="#tab-gallery">组图</a></li>
</ul>

<div class="tab-content">
    <div id="tab-base" class="tab-pane active">
        <div class="panel-body">
            <?php if($model->getIsNewRecord()){ ?>
            <div class="form-group field-goods-grab">
                <label class="control-label col-sm-2" for="goods-grab">抓取</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input id="goods-grab" type="text" class="form-control" placeholder="输入第三方网点产品地址...">
                        <span class="input-group-btn">
                            <button id="goods-grab-button" class="btn btn-default" type="button">确定</button>
                        </span>
                    </div>

                    <div class="help-block help-block-error "></div>
                </div>
            </div>
            <?php } ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'category_id')->select(['class' => 'admin\\models\\Category']) ?>
            <?= $form->field($model, 'preview')->image() ?>
            <?= $form->field($model, 'bar_code')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'weight')->textUnitInput('Kg', ['maxlength' => true]) ?>
            <?= $form->field($model, 'content')->editor() ?>
            <?= $form->field($model, 'status')->radioList($model->getStatusSelectData()) ?>

            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <div id="tab-gallery" class="tab-pane">
        <div class="panel-body">
            <div class="publish-gallery">
                <ul id="publish-gallery" class="list-unstyled clearfix">
                    <li class="gallery-item">
                        <input id="upload-gallery" name="file_upload" type="file" multiple="multiple"/>
                    </li>
                    <?php if(isset($model->galleries['image'])){ ?>
                    <?php foreach($model->galleries['image'] as $index => $image){ ?>
                    <li class="gallery-item sortable-item">
                        <a class="close" href="javascript:void(0)"></a>
                        <div class="notice"></div>
                        <div class="gallery-image">
                            <img src="<?= $model->galleries['thumb'][$index] ?>">
                            <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[thumb][]" type="hidden" value="<?= $model->galleries['thumb'][$index] ?>" />
                            <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[image][]" type="hidden" value="<?= $image ?>" />
                            <input name="<?= Html::getInputName($model, 'galleries') ?>[description][]" type="hidden" value="<?= $model->galleries['description'][$index] ?>" />
                        </div>
                        <div class="gallery-desc"><?= $model->galleries['description'][$index] ?></div>
                    </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>

            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script id="galleryItem" type="text/html">
    <a class="close" href="javascript:void(0)"></a>
    <div class="notice"></div>
    <div class="gallery-image">
        <img src="{{ d.thumb }}">
        <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[thumb][]" type="hidden" value="{{ d.thumb }}" />
        <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[image][]" type="hidden" value="{{ d.path }}" />
        <input name="<?= Html::getInputName($model, 'galleries') ?>[description][]" type="hidden" value="{{ d.name }}" />
    </div>
    <div class="gallery-desc">{{ d.name }}</div>
</script>

<script id="grabGalleries" type="text/html">
    {{#  for(var i = 0; i < d.images.length; i++){ }}
    <li class="gallery-item sortable-item" id="uploadifive-upload-gallery-file-{{ i }}">
        <a class="close" href="javascript:void(0)"></a>
        <div class="notice"></div>
        <div class="gallery-image">
            <img src="{{ d.images[i].thumb }}">
            <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[thumb][]" type="hidden" value="{{ d.images[i].thumb }}" />
            <input class="input-image" name="<?= Html::getInputName($model, 'galleries') ?>[image][]" type="hidden" value="{{ d.images[i].path }}" />
            <input name="<?= Html::getInputName($model, 'galleries') ?>[description][]" type="hidden" value="{{ d.images[i].name }}" />
        </div>
        <div class="gallery-desc">{{ d.images[i].name }}</div>
    </li>
    {{#  } }}
</script>

<?php

$csrfToken = Yii::$app->request->getCsrfToken();
$csrfName = Yii::$app->request->csrfParam;
$uploadUrl = Url::to(['upload/image']);
$uploadTimestamp = time();
$uploadToken = md5('laijiusheng_' . $uploadTimestamp);

$grabUrl = Url::to(['ajax/grab']);

$nameInputId = Html::getInputId($model, 'name');
$previewInputId = Html::getInputId($model, 'preview');
$contentInputId = Html::getInputId($model, 'content');

$js = <<<JS

var galleryTpl = $('#galleryItem').html();
var galleriesTpl = $('#grabGalleries').html();

$(document).on('click', '#publish-gallery > .gallery-item > .close', function(){
    $(this).closest('.gallery-item').remove();
    return false;
});

$('#goods-grab-button').click(function(){
    $.post('$grabUrl', {'$csrfName': '$csrfToken', url: $('#goods-grab').val()}, function(data){
        if(data.json){
            $('#$nameInputId').val(data.json.name);
            $('#$contentInputId').summernote('code', data.json.desc);
            $('#$previewInputId').prev('img').attr('src', data.json.preview_static);
            $('#$previewInputId').val(data.json.preview);
            laytpl(galleriesTpl).render(data.json, function(html){
                $('#publish-gallery').children('li').first().nextAll('li').remove();
                $('#publish-gallery').append(html);
            });
        }
    }, 'json');
});

$('#publish-gallery').sortable({items : ".sortable-item"});

$('#upload-gallery').uploadifive({
    uploadScript: '$uploadUrl',
    width: '170',
    height: '170',
    buttonClass: 'add-gallery',
    buttonText: '添加图片',
    fileSizeLimit: '3MB',
    fileType: 'image/gif,image/jpeg,image/png',
    queueID: 'publish-gallery',
    formData: {
        '$csrfName': '$csrfToken',
        'timestamp': '$uploadTimestamp',
        'token': '$uploadToken',
        'width': 340,
        'height': 340
    },
    overrideEvents: [
        'onUploadComplete'
    ],
    itemTemplate: $('<li>').addClass('gallery-item').addClass('sortable-item').addClass('uploadifive-queue-item')
        .append($('<a>').addClass('close').attr('href', 'javascript:void(0)'))
        .append($('<div>').addClass('notice')
            .append($('<span>').addClass('filename'))
            .append($('<span>').addClass('fileinfo')))
        .append($('<div>').addClass('progress').append($('<div>').addClass('progress-bar'))),
    onUploadComplete: function(file, data){
        data = $.parseJSON(data);
        file.queueItem.removeClass('uploadifive-queue-item');
        file.queueItem.find('.close').unbind('click');
        
        laytpl(galleryTpl).render(data, function(html){
            file.queueItem.html(html);
        });
    }
});

JS;

$this->registerJs($js);