<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Group */
/* @var $order \common\models\Order */

$this->title = '拼单号 #' . $model->id;
?>

<div class="box">
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
            <p><?= $model->goods->name ?></p>
            <p><span class="red"><?= $model->quantity ?></span> 人拼，已 <span class="red"><?= $model->joiner ?></span> 人参加
            </p>
            <p>拼单状态：<?= $model->showStatus() ?></p>
            <p>开团时间：<?= date("Y-m-d H:i:s", $model->created_at) ?></p>
        </div>
    </div>
</div>

<div class="box">
    <strong class="red">参与人员：</strong><br/>
    发起人：<?= $model->user->mobile ?><br/>
    <?php foreach ($model->other as $index => $order) { ?>
        <?php if ($index == 0) { ?>参与人：<?php } else { ?>　　　　<?php } ?>
        <?php
        if (Yii::$app->user->id == $model->user_id) {
            echo $order->user->mobile;
        } else {
            echo $order->user->showMobile();
        }
        ?><br/>
    <?php } ?>
</div>