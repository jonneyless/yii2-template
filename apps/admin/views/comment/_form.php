<?php

use admin\assets\PageAsset;
use yii\jui\JuiAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\Comment */
/* @var $form yii\bootstrap\ActiveForm  */

PageAsset::register($this)->init([
    'js' => [
        'js/laytpl/laytpl.js',
        'js/jquery.uploadifive.js',
    ],
]);

JuiAsset::register($this);
?>

<div class="ibox">
    <div class="ibox-content">

        <?php $form = ActiveForm::begin([
            'fieldClass' => ActiveField::className(),
        ]); ?>

        <div class="row">
            <?php if($model->getIsNewRecord()){ ?>
            <div class="col-lg-4">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
            <?php }else{ ?>
            <div class="col-lg-8">
            <?php } ?>
                <?= $form->field($model, 'goods_id')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'status')->radioList($model->getStatusSelectData()) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'goods_score')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'store_score')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'delivery_score')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <?= $form->field($model, 'content')->textarea() ?>

        <div class="publish-gallery" style="padding: 0px;">
            <ul id="publish-gallery" class="list-unstyled clearfix">
                <li class="gallery-item">
                    <input id="upload-gallery" name="file_upload" type="file" multiple="multiple"/>
                </li>
                <?php if(isset($model->imgs['image'])){ ?>
                    <?php foreach($model->imgs['image'] as $index => $image){ ?>
                        <li class="gallery-item sortable-item">
                            <a class="close" href="javascript:void(0)"></a>
                            <div class="notice"></div>
                            <div class="gallery-image">
                                <img src="<?= $model->imgs['thumb'][$index] ?>">
                                <input class="input-image" name="<?= Html::getInputName($model, 'imgs') ?>[thumb][]" type="hidden" value="<?= $model->imgs['thumb'][$index] ?>" />
                                <input class="input-image" name="<?= Html::getInputName($model, 'imgs') ?>[image][]" type="hidden" value="<?= $image ?>" />
                            </div>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>

        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script id="galleryItem" type="text/html">
    <a class="close" href="javascript:void(0)"></a>
    <div class="notice"></div>
    <div class="gallery-image">
        <img src="{{ d.thumb }}">
        <input class="input-image" name="<?= Html::getInputName($model, 'imgs') ?>[thumb][]" type="hidden" value="{{ d.thumb }}" />
        <input class="input-image" name="<?= Html::getInputName($model, 'imgs') ?>[image][]" type="hidden" value="{{ d.path }}" />
    </div>
</script>

<?php

$csrfToken = Yii::$app->request->getCsrfToken();
$csrfName = Yii::$app->request->csrfParam;
$uploadUrl = Url::to(['upload/image']);
$uploadTimestamp = time();
$uploadToken = md5('laijiusheng_' . $uploadTimestamp);

$js = <<<JS

var galleryTpl = $('#galleryItem').html();

$(document).on('click', '#publish-gallery > .gallery-item > .close', function(){
    $(this).closest('.gallery-item').remove();
    return false;
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
