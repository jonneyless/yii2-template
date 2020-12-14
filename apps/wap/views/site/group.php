<?php

use libs\Utils;
use wap\assets\PageAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \common\models\Group */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
        'js/jquery.timer.js',
    ],
]);

$this->title = '拼单号 #' . $model->id;

$event_name = '爱拼才会赢';
?>

    <div class="box">
        <div class="alert alert-success text-center" style="padding: 10px;" role="alert">
            <span class="glyphicon glyphicon-ok-sign"></span> <span class="blue">建行“<?= $event_name ?>”活动进行中！</span>
        </div>

        <div class="order-item order-item-big clearfix">
            <div class="order-item-preview">
                <?php if ($model->one_delivery) { ?>
                    <span class="sign sign-helpme">帮我拼</span>
                <?php } else { ?>
                    <span class="sign sign-letgo">一起拼</span>
                <?php } ?>
                <img src="<?= $model->goods->getPreview(150, 150) ?>"/>
            </div>

            <div class="order-item-info">
                <strong class="title"><?= $model->goods->name ?></strong><br/>
                <?php if ($model->goods->sub_name) { ?>
                    <span class="gray"><?= $model->goods->sub_name ?></span><br/>
                <?php } ?>
                <?php if ($model->one_delivery == 1) { ?>
                    小伙伴有了您的助力就能获得活动商品啦！<br/>
                <?php } else { ?>
                    小伙伴邀请你一起参加活动，商品每人一份！<br/>
                <?php } ?>
                参与人：<span class="red">￥<?= $model->price ?></span>/人 &nbsp;
                <span class="red"><?= $model->quantity ?></span>人拼
            </div>
        </div>
    </div>

    <div class="box box-nobg text-center">剩余
        <div id="timer"></div>
        结束
    </div>

    <div class="box text-center">
        <img src="<?= Url::to(['site/qrcode', 'url' => Utils::fullUrl(['site/join', 'id' => $model->id])]) ?>"/>
        <p class="qrcode-notice">发起人<strong>截图保存</strong>邀请好友扫码参与吧！</p>
        <p class="qrcode-notice">参与人<strong>识别二维码</strong>完成付款，参与活动。</p>
    </div>

    <div class="guider">
        <div class="clearfix">
            <div class="pull-left">拼单玩法</div>
        </div>

        <ul class="list-unstyled">
            <li class="active">
                <span>1</span>
                选择商品<br>完成支付
            </li>
            <li class="active">
                <span>2</span>
                截图分享<br>邀请好友
            </li>
            <li class="active">
                <span>3</span>
                多名好友<br>完成支付
            </li>
            <li>
                <span>4</span>
                满足条件<br>拼团成功
            </li>
        </ul>
    </div>

<?php

$time = $model->expiry - time();

$hh = sprintf('%02d', intval($time / 3600));

$leave = $time % 3600;

$mm = sprintf('%02d', intval($leave / 60));

$ss = sprintf('%02d', intval($leave % 60));

$js = <<<JS
    
    $('#timer').jqTimer({
        beginTime: '$hh$mm$ss',
        countdown: true
    });

JS;

$this->registerJs($js);
