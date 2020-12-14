<?php

use admin\assets\PageAsset;
use yii\jui\JuiAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use ijony\admin\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $model admin\models\form\Goods */
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
    <li class=""><a data-toggle="tab" href="#tab-attr">属性</a></li>
</ul>

<div class="tab-content">
    <div id="tab-base" class="tab-pane active">
        <div class="panel-body">
            <div class="form-group field-goods-grab">
                <label class="control-label col-sm-2" for="goods-grab">抓取</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input id="goods-grab" type="text" class="form-control" placeholder="输入第三方网点商品地址...">
                        <span class="input-group-btn">
                            <button id="goods-grab-button" class="btn btn-default" type="button">确定</button>
                        </span>
                    </div>

                    <div class="help-block help-block-error "></div>
                </div>
            </div>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'bar_code')->textButtonInput('同步', ['maxlength' => true, 'readyonly' => true, 'buttonOptions' => ['id' => 'sync'], 'disabled' => !$model->goods->getIsNewRecord()]) ?>
            <?= $form->field($model, 'category_id')->select(['class' => 'admin\\models\\Category']) ?>
            <?= $form->field($model, 'store_category_id')->dropDownList($model->getStoreCategorySelectData(), ['prompt' => '请选择']) ?>
            <?= $form->field($model, 'preview')->image() ?>
            <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'weight')->textUnitInput('Kg', ['maxlength' => true]) ?>
            <?= $form->field($model, 'original_price')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'member_price')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'stock')->textInput(['maxlength' => true, 'disabled' => true]) ?>
            <?= $form->field($model, 'sell')->textInput(['maxlength' => true, 'disabled' => true]) ?>
            <?= $form->field($model, 'content')->editor() ?>
            <?= $form->field($model, 'shelves_at')->date(['readonly' => true]) ?>
            <?= $form->field($model, 'is_hot')->radioList($model->getIsHotSelectData()) ?>
            <?= $form->field($model, 'is_recommend')->radioList($model->getIsRecommendSelectData()) ?>
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

    <div id="tab-attr" class="tab-pane">
        <div class="panel-body">
            <div class="table-responsive col-sm-4 col-sm-offset-4">
                <table class="table table-stripped table-bordered">
                    <thead>
                    <tr>
                        <th>属性名称</th>
                        <th>属性值（一行一个值）</th>
                        <th width="50"><a id="add-attr" class="btn btn-primary btn-xs" href="javascript:void(0)">增加</a></th>
                    </tr>
                    </thead>
                    <tbody id="attr-list">
                    <?php if(isset($model->attrs['name'])){ ?>
                    <?php foreach($model->attrs['name'] as $index => $name){ ?>
                    <tr>
                        <td><input type="text" name="<?= Html::getInputName($model, 'attrs') ?>[name][]" class="form-control" value="<?= $name ?>" title="属性名称"/></td>
                        <td><textarea resize="vertical" rows="6" name="<?= Html::getInputName($model, 'attrs') ?>[value][]" class="form-control" title="属性值"><?= $model->attrs['value'][$index] ?></textarea></td>
                        <td><a class="btn btn-danger btn-xs del-attr" href="javascript:void(0)">删除</a></td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr>
                        <td><input type="text" name="<?= Html::getInputName($model, 'attrs') ?>[name][]" class="form-control" value="" title="属性名称" /></td>
                        <td><textarea resize="vertical" rows="6" name="<?= Html::getInputName($model, 'attrs') ?>[value][]" class="form-control" title="属性值"></textarea></td>
                        <td><a class="btn btn-danger btn-xs del-attr" href="javascript:void(0)">删除</a></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-4 text-center">
                    <?= Html::resetButton('重置', ['class' => 'btn btn-white']) ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($model->getIsNewRecord()){ ?>
<?= Html::activeHiddenInput($model, 'product_id') ?>
<?php } ?>
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

$storeDropDown = Html::getInputId($model, 'store_id');
$storeCategoryDropDown = Html::getInputId($model, 'store_category_id');
$storeDataUrl = Url::to(['ajax/filter-store']);
$storeCategoryDataUrl = Url::to(['ajax/options', 'model' => \admin\models\StoreCategory::className(), 'field' => 'store_id']);
$grabUrl = Url::to(['ajax/grab']);

$nameInputId = Html::getInputId($model, 'name');
$barcodeInputId = Html::getInputId($model, 'bar_code');
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

$('#$storeDropDown').change(function(){
    var store_id = $(this).val();
    
    $('#$storeCategoryDropDown option:first').nextAll().remove();
    
    if(store_id){
        $.post('$storeCategoryDataUrl', {'$csrfName': '$csrfToken', 'value': store_id}, function(data){
            if(data.html){
                $('#$storeCategoryDropDown').html(data.html);
            }
        }, 'json');
    }
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

$('#add-attr').click(function(){
    var tr = $('#attr-list > tr').eq(0).clone();
    
    tr.find('input').val('');
    
    $('#attr-list').append(tr);
});

$(document).on('click', '.del-attr', function(){
    var tr = $(this).closest('tr');
    
    if(tr.parent().children('tr').length > 1){
        tr.remove();
    }else{
        tr.find('input').val('');
    }
});

$('#sync').click(function(){
    var barcode = $('#$barcodeInputId').val();
    var form = $(this).closest('form');
    var url = form.attr('action');
    var reg = /&barcode=.*/g;
    
    if(barcode){
        window.location.href = url.replace(reg, '') + '&barcode=' + barcode;
    }
});

JS;

$this->registerJs($js);