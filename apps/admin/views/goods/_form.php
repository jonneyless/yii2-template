<?php

use libs\Utils;
use admin\assets\PageAsset;
use common\widgets\ActiveField;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\jui\JuiAsset;

/* @var $this yii\web\View */
/* @var $model admin\models\GoodsForm */
/* @var $form yii\bootstrap\ActiveForm */

PageAsset::register($this)->init([
    'js' => [
        'js/laytpl/laytpl.js',
        'js/jquery.uploadifive.js',
    ],
]);

JuiAsset::register($this);
?>

    <div class="panel panel-default tabs-box">
        <div class="panel-heading text-center">
            <ul class="list-unstyled tabs">
                <li><a href="javascript:void(0)">基本信息</a></li>
                <li><a href="javascript:void(0)">商品组图</a></li>
                <li><a href="javascript:void(0)">商品属性</a></li>
                <li><a href="javascript:void(0)">详细介绍</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="goods-form">

                <?php $form = ActiveForm::begin([
                    'options' => [
                        'enctype' => 'multipart/form-data',
                    ],
                    'fieldClass' => ActiveField::className(),
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inline' => true,
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-4',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-6',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                ]); ?>

                <div class="tabs-content">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'sub_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'category_id', [
                        'horizontalCssClasses' => [
                            'wrapper' => 'col-sm-3',
                        ],
                    ])->dropDownList(\common\models\Category::getSelectDatas()) ?>

                    <?= $form->field($model, 'preview')->image() ?>

                    <?= $form->field($model, 'stock', [
                        'horizontalCssClasses' => [
                            'wrapper' => 'col-sm-2',
                        ],
                    ])->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'sales', [
                        'horizontalCssClasses' => [
                            'wrapper' => 'col-sm-2',
                        ],
                    ])->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'price', [
                        'horizontalCssClasses' => [
                            'wrapper' => 'col-sm-3',
                        ],
                    ])->textUnitInput('元', ['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

                    <?= $form->field($model, 'status')->radioList(\common\models\Goods::getShelveStatus()) ?>

                </div>

                <div class="tabs-content">
                    <div class="publish-gallery">
                        <ul id="publish-gallery" class="list-unstyled clearfix">
                            <li class="gallery-item">
                                <input id="upload-gallery" name="file_upload" type="file" multiple="multiple"/>
                            </li>
                            <?php if ($model->galleries) { ?>
                                <?php foreach ($model->galleries as $gallery) { ?>
                                    <li class="gallery-item sortable-item">
                                        <a class="close" href="javascript:void(0)"></a>
                                        <div class="notice"></div>
                                        <div class="gallery-image">
                                            <img src="<?= Utils::galleryImage($gallery->image, 340, 340) ?>">
                                            <input class="input-image" name="<?= Html::getInputName($model, 'gallery') ?>[image][]" type="hidden" value="<?= $gallery->image ?>"/>
                                        </div>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <?php $attrInputName = Html::getInputName($model, 'attrs'); ?>

                <div class="tabs-content">
                    <table class="line-input">
                        <tr>
                            <td class="col-sm-1 text-center"></td>
                            <td class="col-sm-5 text-center">属性名</td>
                            <td class="col-sm-5 text-center">属性值</td>
                            <td class="col-sm-1 text-center"></td>
                        </tr>
                        <?php if (isset($model->attrs['name'])) { ?>
                            <?php foreach ($model->attrs['name'] as $index => $name) { ?>
                                <?php
                                if (!$name) {
                                    continue;
                                }
                                if (!isset($model->attrs['value'][$index])) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="text" name="<?= $attrInputName ?>[name][]" class="form-control" value="<?= $name ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" name="<?= $attrInputName ?>[value][]" class="form-control" value="<?= $model->attrs['value'][$index] ?>"/>
                                    </td>
                                    <td><a class="icon-link icon-link-middle" role="del-line" href="javascript:void(0)"><span class="glyphicon glyphicon-minus"></span></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td><input type="text" name="<?= $attrInputName ?>[name][]" class="form-control" value=""/>
                            </td>
                            <td><input type="text" name="<?= $attrInputName ?>[value][]" class="form-control" value=""/>
                            </td>
                            <td>
                                <a class="icon-link icon-link-middle" role="add-line" href="javascript:void(0)"><span class="glyphicon glyphicon-plus"></span></a>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="tabs-content">
                    <?= $form->field($model, 'content', [
                        'horizontalCssClasses' => [
                            'label' => '',
                            'offset' => '',
                            'wrapper' => 'col-sm-12',
                            'error' => '',
                            'hint' => '',
                        ],
                    ])->label(false)->editor() ?>
                </div>

                <div class="form-group text-center">
                    <?= Html::submitButton(!$model->goods ? '添加' : '更新', ['class' => !$model->goods ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width: 200px;']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

    <script id="galleryItem" type="text/html">
        <a class="close" href="javascript:void(0)"></a>
        <div class="notice"></div>
        <div class="gallery-image">
            <img src="{{ d.thumb }}">
            <input class="input-image" name="<?= Html::getInputName($model, 'gallery') ?>[image][]" type="hidden" value="{{ d.path }}"/>
        </div>
    </script>

<?php

$uploadUrl = Url::to(['upload/image']);
$uploadTimestamp = time();
$uploadToken = md5(Yii::$app->params['md5.authKey'] . $uploadTimestamp);
$js = <<<JS

var galleryTpl = $('#galleryItem').html();

$('.tabs-box').each(function(index, obj){
    var tabs = $(obj).children('.panel-heading').children('.tabs').find('a');
    var tabsContent = $(obj).children('.panel-body').find('.tabs-content');
    
    tabs.eq(0).addClass('active');
    tabsContent.hide();
    tabsContent.eq(0).show();
    
    tabs.click(function(){
        tabs.removeClass('active');
        $(this).addClass('active');
        tabsContent.hide();
        tabsContent.eq($(this).parent('li').prevAll('li').length).show();
        return false;
    });
});

$(document).on('click', 'a[role="del-line"]', function(){
    $(this).closest('tr').remove();
});

$(document).on('click', 'a[role="add-line"]', function(){
    var oldTr = $(this).closest('tr');
    var newTr = oldTr.clone();
    newTr.find('a').attr('role', 'del-line');
    newTr.find('span').attr('class', 'glyphicon glyphicon-minus');
    oldTr.before(newTr);
    oldTr.find('input').val('');
});

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
        _csrf: $('input[name="_csrf"]').val(),
        timestamp: '$uploadTimestamp',
        token: '$uploadToken',
        width: 170,
        height: 170
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
