<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Group */

$this->title = '拼单号 #' . $model->id;
?>

<div class="box">
    <div class="alert alert-success text-center" style="padding: 10px;" role="alert">
        <span class="glyphicon glyphicon-ok-sign"></span> 拼单已成功</span>
    </div>

    <div class="order-item order-item-big<?php if ($model->isOver()) { ?> order-success<?php } ?><?php if ($model->isCancel()) { ?> order-failure<?php } ?> clearfix">
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
            <span class="red"><?= $model->quantity ?></span> 人拼，已 <span class="red"><?= $model->joiner ?></span> 人参加
        </div>
    </div>
</div>

<div class="box text-center">
    <p class="qrcode-notice">
        请登录“<a href="<?= \yii\helpers\Url::to(['user/order']) ?>"><strong>我的订单</strong></a>”查看相关信息<br/>
        <?php if ($model->one_delivery == 1) { ?>
            <?php if ($model->goods->is_virtual == 1) { ?>
                <span class="gray">注：电子券码只发送给发起人</span>
            <?php } else { ?>
                <span class="gray">注：商品只邮寄给发起人</span>
            <?php } ?>
        <?php } ?>
    </p>
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
        <li class="active">
            <span>4</span>
            满足条件<br>拼团成功
        </li>
    </ul>
</div>
