<?php

use wap\assets\PageAsset;

/* @var $this yii\web\View */
/* @var $order \common\models\Order */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$this->title = '订单支付';
?>
<div class="box box-nobg">
    <div class="alert alert-warning" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign"></span> 订单有效期<?= Yii::$app->params['order.pay.expire']; ?>分钟，请在有效期内及时付款。
    </div>
</div>

<div class="box">
    <dl class="line-box">
        <dt>商品名称：</dt>
        <dd><?= $order->goods->name ?></dd>
    </dl>
    <dl class="line-box">
        <dt>订单编号：</dt>
        <dd><?= $order->id ?></dd>
    </dl>
    <dl class="line-box">
        <dt>商家名称：</dt>
        <dd>建设银行</dd>
    </dl>
    <dl class="line-box">
        <dt>支付金额：</dt>
        <dd>￥<?= $order->amount ?></dd>
    </dl>

    <?php if (isset($pay['params'])) { ?>
        <form action="<?= $pay['url'] ?>" method="get">
            <?php foreach ($pay['params'] as $name => $value) { ?>
                <input type="hidden" name="<?= $name ?>" value="<?= $value ?>"/>
            <?php } ?>
            <input type="submit" class="btn btn-primary btn-lg form-control" value="建 行 支 付"/>
        </form>
    <?php } else { ?>
        <form action="http://www.gshccb.com//mobile/wap_ccb_post.php" method="post">
            <input type="hidden" name="params" value="<?= $pay ?>"/>
            <input type="submit" class="btn btn-primary btn-lg form-control" value="建 行 支 付"/>
        </form>
    <?php } ?>
</div>