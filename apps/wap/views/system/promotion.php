<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = "注册会员";
?>

<div class="login-top"></div>
<div class="form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <label><?= Html::activeLabel($model, 'mobile') ?></label>
        <div class="input">
            <?= Html::activeTextInput($model, 'mobile', ['placeholder' => '请输入手机号码', 'autocomplete' => 'off']) ?>
        </div>
    </div>

    <div class="form-group">
        <label><?= Html::activeLabel($model, 'vcode') ?></label>
        <div class="input input-group">
            <?= Html::activeTextInput($model, 'vcode', ['placeholder' => '请输入验证码']) ?>
            <a id="send-vcode" class="red" href="javascript:void(0)">发送</a>
        </div>
    </div>

    <div class="form-group">
        <label><?= Html::activeLabel($model, 'password') ?></label>
        <div class="input">
            <?= Html::activePasswordInput($model, 'password', ['placeholder' => '请输入密码']) ?>
        </div>
    </div>

    <div class="form-group">
        <label><?= Html::activeLabel($model, 'referee') ?></label>
        <div class="input">
            <?= Html::activeTextInput($model, 'referee', ['readonly' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-red']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php

$error = $model->getFirstErrors();
$error = current($error);
$mobileId = Html::getInputId($model, 'mobile');
$vcodeId = Html::getInputId($model, 'vcode');
$vcodeUrl = Url::to(['ajax/vcode']);
$areaId = Html::getInputId($model, 'area');
$areaIdHidden = Html::getInputId($model, 'area_id');
$areaUrl = Url::to(['ajax/area']);

$js = <<<JS
    var error = '$error';
    
    $('#send-vcode').click(function(){
        var mobile = $('#$mobileId').val();
        
        if(!mobile){
            layer.open({
                content: '请输入手机号！',
                skin: 'msg',
                time: 2,
            });
            
            return;
        }
        
        $.post('$vcodeUrl', {'mobile': mobile, 'event': 'signup'}, function(data){
            if(data.msg){
                layer.open({
                    content: data.msg,
                    skin: 'msg',
                    time: 2,
                });
            }
            
            if(data.url){
                window.location.href = data.url;
            }
        }, 'json');
    });
    
    if(error){
        layer.open({
            content: error,
            skin: 'msg',
            time: 2,
        });
    }
JS;

$this->registerJs($js);
