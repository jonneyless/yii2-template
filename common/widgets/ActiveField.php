<?php

namespace common\widgets;

use common\models\Area;
use admin\assets\PageAsset;
use libs\SMS;
use libs\Utils;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * 动态表单重构类
 *
 * @package common\widgets
 */
class ActiveField extends \yii\bootstrap\ActiveField
{

    /**
     * 带单位文本框
     *
     * @param       $unit
     * @param array $options
     *
     * @return $this
     */
    public function textUnitInput($unit, $options = [])
    {
        if (!$unit) {
            return $this->textInput($options);
        }

        $inputGroupClass[] = 'input-group';

        if (isset($options['autoWidth'])) {
            $inputGroupClass[] = 'input-group-inline';
            $options['class'] = ['form-control', 'form-control-inline'];
            unset($options['autoWidth']);
        }

        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::tag(
            'div',
            Html::activeTextInput($this->model, $this->attribute, $options) .
            Html::tag(
                'div',
                $unit,
                [
                    'class' => 'input-group-addon',
                ]
            ),
            [
                'class' => $inputGroupClass,
            ]
        );

        return $this;
    }

    /**
     * 带验证码发送按钮手机表单
     *
     * @param array $options
     *
     * @return $this
     */
    public function mobileVcode($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::tag(
            'div',
            Html::activeTextInput($this->model, $this->attribute, $options) .
            Html::tag(
                'div',
                Html::button(
                    '发送验证码',
                    [
                        'class' => 'btn btn-danger sendSMS',
                    ]
                ),
                [
                    'class' => 'input-group-btn',
                ]
            ),
            [
                'class' => 'input-group',
            ]
        );

        $time = SMS::getCountDown();
        if (!isset($options['mobileInputId'])) {
            $mobileInputId = Html::getInputId($this->model, $this->attribute);
        } else {
            $mobileInputId = $options['mobileInputId'];
            unset($options['mobileInputId']);
        }
        $url = Url::to(['ajax/vcode']);
        $js = <<<JS
        
// 发送验证码        
$('.sendSMS').each(function(i, obj) {
    sendButton = $(obj);
    sendButton.append($('<span>'));
    showSpan = sendButton.children('span');

    if(time > 0){
        sendButton.removeClass('btn-gray');
        sendButton.attr('countDown', 1);
        showSpan.text('(' + time + ')');
        t = setTimeout('countDown()', 1000);
    }

    sendButton.click(function(){
        var mobile = $('#$mobileInputId').val();

        if(sendButton.attr('countDown') == 1){
            if(layer){
                layer.alert('请不要频繁点击“发送验证码”按钮！');
            }else{
                alert('请不要频繁点击“发送验证码”按钮！');
            }
            return false;
        }

        $.post('$url', {mobile: mobile, _csrf: $('input[name="_csrf"]').val()}, function(data){
            if(data.result == 1){
                sendButton.removeClass('btn-gray');
                sendButton.attr('countDown', 1);
                sendButton.children('span').text('(60)');
                t = setTimeout('countDown()', 1000);
            }
            
            if(data.message){
                if(layer){
                    layer.alert(data.message);
                }else{
                    alert(data.message);
                }
            }
        }, 'json');
    });
});

JS;

        Yii::$app->getView()->registerJs($js, View::POS_READY, 'mobileVcode');

        $js = <<<JS
var t;
var time = $time;
var sendButton;
var showSpan;

function countDown(){
    if(showSpan.text() == '(0)'){
        showSpan.remove();
        $('.sendSMS').addClass('btn-gray');
        $('.sendSMS').removeAttr('countDown');
        return;
    }

    if(time == 0){
        time = 60;
    }

    time -= 1;
    showSpan.text('(' + time + ')');
    t = setTimeout('countDown()', 1000);
}
JS;

        Yii::$app->getView()->registerJs($js, View::POS_BEGIN, 'mobileVcode');

        return $this;
    }

    /**
     * 单图上传控件
     *
     * @param array $options
     *
     * @return $this
     */
    public function image($options = [])
    {
        $circle = false;
        $width = 170;

        if (isset($options['circle'])) {
            $circle = $options['circle'];
            unset($options['circle']);
        }

        if (isset($options['width'])) {
            $width = $options['width'];
            unset($options['width']);
        }

        $inputId = Html::getInputId($this->model, $this->attribute);
        $inputName = Html::getInputName($this->model, $this->attribute);
        $image = Html::getAttributeValue($this->model, $this->attribute);

        if ($circle) {
            $img = Html::img(Utils::galleryImage($image, $width, $width), ['class' => 'img-circle', 'title' => '点击上传', 'width' => $width, 'height' => $width]);
        } else {
            $img = Html::img(Utils::galleryImage($image, $width, $width), ['width' => $width, 'height' => $width, 'title' => '点击上传']);
        }

        $this->parts['{input}'] = Html::tag('div', $img, ['id' => 'upload-thumb', 'class' => 'upload-thumb'])
            . Html::fileInput('file_upload', '', ['id' => 'upload-image'])
            . Html::hiddenInput($inputName, $image, ['id' => $inputId]);

        $uploadUrl = Url::to(['upload/image']);
        $uploadTimestamp = time();
        $uploadToken = md5(Yii::$app->params['md5.authKey'] . $uploadTimestamp);
        $js = <<<JS

// 图片上传控件
$('#upload-image').uploadifive({
    uploadScript: '$uploadUrl',
    width: '$width',
    height: '$width',
    buttonClass: 'upload-image-button',
    buttonText: '',
    multi : false,
    fileSizeLimit: '3MB',
    fileType: 'image/gif,image/jpeg,image/png',
    removeCompleted: true,
    queueID : 'upload-thumb',
    formData: {
        _csrf: $('input[name="_csrf"]').val(),
        timestamp: '$uploadTimestamp',
        token: '$uploadToken',
        width: $width,
        height: $width
    },
    itemTemplate : '<div class="uploadifive-queue-item">\
            <div class="progress">\
                <div class="progress-bar"></div>\
            </div>\
        </div>',
    onUploadComplete: function(file, data){
        data = $.parseJSON(data);
        if(data.thumb){
            $('#upload-thumb img').attr('src', data.thumb);
        }
        $('#$inputId').val(data.path);
    }
});

JS;

        PageAsset::register(Yii::$app->getView())->init([
            'js' => [
                'js/jquery.uploadifive.js',
            ],
        ]);
        Yii::$app->getView()->registerJs($js, View::POS_READY, 'upload-image');

        return $this;
    }

    /**
     * 编辑器
     *
     * @param array $options
     *
     * @return $this
     */
    public function editor($options = [])
    {
        $inputId = Html::getInputId($this->model, $this->attribute);

        $this->textarea($options);

        $uploadJson = Url::to(['file/upload']);
        $fileManagerJson = Url::to(['file/filemanager']);
        $js = <<<JS

KindEditor.create('#$inputId', {
    width: "100%",
    height: "480px",
    themeType: "simple",
    langType: "zh-CN",
    afterChange: function(){
        this.sync();
    },
    resizeType: "1",
    allowFileManager: "true",
    allowImageUpload: "true",
    allowFlashUpload: "false",
    allowMediaUpload: "false",
    allowFileUpload: "false",
    uploadJson: '$uploadJson',
    fileManagerJson: '$fileManagerJson'
});

JS;

        PageAsset::register(Yii::$app->getView())->init([
            'css' => [
                'js/editor/themes/default/default.css',
            ],
            'js' => [
                'js/editor/kindeditor-all-min.js',
            ],
        ]);
        Yii::$app->getView()->registerJs($js, View::POS_READY, 'editor');

        return $this;
    }

    /**
     * 地区选择
     *
     * @return $this
     */
    public function area()
    {
        $inputName = Html::getInputName($this->model, $this->attribute);
        $areaIds = Html::getAttributeValue($this->model, $this->attribute);

        if ($areaIds) {
            if (!is_array($areaIds)) {
                $areaIds = Area::getParentLine(intval($areaIds));
            }
        } else {
            $areaIds = [0];
        }

        $parantId = 0;
        foreach ($areaIds as $areaId) {
            $selects[] = Html::dropDownList($inputName, $areaId, Area::getSelectData($parantId), [
                'class' => 'form-control form-control-inline',
                'ajax-select' => Url::to(['ajax/select-area']),
            ]);
            $parantId = $areaId;
        }

        $area = Area::findOne($parantId);
        if ($area && $area->child) {
            $selects[] = Html::dropDownList($inputName, '', Area::getSelectData($parantId), [
                'class' => 'form-control form-control-inline',
                'ajax-select' => Url::to(['ajax/select-area']),
            ]);
        }

        $this->parts['{input}'] = join("", $selects);

        $js = <<<JS
        
$(document).off('change', 'select[ajax-select]');
$(document).on('change', 'select[ajax-select]', function(){
    var select = $(this);
    var url = $(this).attr('ajax-select');
    var parentId = $(this).val();
    var inputName = '$inputName';
    
    select.nextAll('select').remove();
    
    $.post(url, {_crsf: $('input[name="_csrf"]').val(), parentId: parentId, inputName: inputName}, function(datas){
        if(datas.html){
            select.after(datas.html);
        }
    }, 'json');
});

JS;

        Yii::$app->getView()->registerJs($js, View::POS_READY, 'ajax-select');

        return $this;
    }

    public function date($options = [])
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $inputName = Html::getInputName($this->model, $this->attribute);
        $value = Html::getAttributeValue($this->model, $this->attribute);

        if (!$value) {
            $value = '';
        } else {
            $value = is_integer($value) ? date("Y-m-d", $value) : $value;
        }

        if (isset($options['template'])) {
            $template = $options['template'];
            unset($options['template']);
        }

        if (!isset($options['class'])) {
            $options['class'] = 'form-control';
        }

        $options['id'] = $inputId;

        $this->parts['{input}'] = Html::tag(
            'div',
            Html::textInput($inputName, $value, $options) .
            Html::tag(
                'span',
                Html::button(
                    Html::tag(
                        'span',
                        '',
                        [
                            'class' => 'glyphicon glyphicon-calendar',
                            'aria-hidden' => 'true',
                        ]
                    ),
                    [
                        'class' => 'btn btn-default',
                        'onclick' => "laydate({elem: '#" . $inputId . "', format: 'YYYY-MM-DD hh:mm:ss', istoday: false});",
                    ]
                ),
                [
                    'class' => 'input-group-btn',
                ]
            ),
            [
                'class' => 'input-group',
            ]
        );

        $js = <<<JS
        
laydate({
    elem: '#$inputId',
    event: 'focus',
    format: 'YYYY-MM-DD hh:mm:ss',
    istime: true,
    istoday: false
});

JS;

        Yii::$app->getView()->registerJs($js, View::POS_READY, 'date_' . $inputId);
        Yii::$app->getView()->registerJsFile('web/js/laydate/laydate.js', ['depends' => 'admin\assets\AppAsset']);

        return $this;
    }

    public function betweenDate($endAttr, $options = [])
    {
        $hasTime = true;

        if (isset($options['has_time'])) {
            $hasTime = $options['has_time'];
        }

        if ($hasTime) {
            $dateTemplate = "Y-m-d H:i:ss";
            $layTemplate = "YYYY-MM-DD hh:mm:ss";
        } else {
            $dateTemplate = "Y-m-d";
            $layTemplate = "YYYY-MM-DD";
        }

        $beginInputId = Html::getInputId($this->model, $this->attribute);
        $endInputId = Html::getInputId($this->model, $endAttr);

        $beginInputName = Html::getInputName($this->model, $this->attribute);
        $endInputName = Html::getInputName($this->model, $endAttr);

        $beginValue = Html::getAttributeValue($this->model, $this->attribute);
        $endValue = Html::getAttributeValue($this->model, $endAttr);

        if (!$beginValue) {
            $beginValue = '';
        } else {
            $beginValue = is_integer($beginValue) ? date($dateTemplate, $beginValue) : $beginValue;
        }

        if (!$endValue) {
            $endValue = '';
        } else {
            $endValue = is_integer($endValue) ? date($dateTemplate, $endValue) : $endValue;
        }

        $template = '起 %s 止 %s';

        if ($this->form->layout == 'default') {
            $template = '<div>起 %s 止 %s</div>';
        }

        if (isset($options['template'])) {
            $template = $options['template'];
            unset($options['template']);
        }

        if (!isset($options['class'])) {
            $options['class'] = 'form-control form-control-datetime';
        }

        $options['id'] = $beginInputId;

        $beginInput = Html::tag(
            'div',
            Html::textInput($beginInputName, $beginValue, $options) .
            Html::tag(
                'span',
                Html::button(
                    Html::tag(
                        'span',
                        '',
                        [
                            'class' => 'glyphicon glyphicon-calendar',
                            'aria-hidden' => 'true',
                        ]
                    ),
                    [
                        'class' => 'btn btn-default',
                        'onclick' => "laydate({elem: '#" . $beginInputId . "', format: '$layTemplate', istime: true, istoday: false});",
                    ]
                ),
                [
                    'class' => 'input-group-btn',
                ]
            ),
            [
                'class' => 'input-group input-group-inline',
            ]
        );

        $options['id'] = $endInputId;

        $endInput = Html::tag(
            'div',
            Html::textInput($endInputName, $endValue, $options) .
            Html::tag(
                'span',
                Html::button(
                    Html::tag(
                        'span',
                        '',
                        [
                            'class' => 'glyphicon glyphicon-calendar',
                            'aria-hidden' => 'true',
                        ]
                    ),
                    [
                        'class' => 'btn btn-default',
                        'onclick' => "laydate({elem: '#" . $endInputId . "', format: '$layTemplate', istime: true, istoday: false});",
                    ]
                ),
                [
                    'class' => 'input-group-btn',
                ]
            ),
            [
                'class' => 'input-group input-group-inline',
            ]
        );

        $this->parts['{input}'] = sprintf($template, $beginInput, $endInput);

        $js = <<<JS
        
laydate({
    elem: '#$beginInputId',
    format: '$layTemplate',
    istime: true,
    istoday: false
});

laydate({
    elem: '#$endInputId',
    format: '$layTemplate',
    istime: true,
    istoday: false
});

JS;

        Yii::$app->getView()->registerJs($js, View::POS_READY, 'between_date_' . $beginInputId);
        Yii::$app->getView()->registerJsFile('web/js/laydate/laydate.js', ['depends' => 'admin\assets\AppAsset']);

        return $this;
    }
}