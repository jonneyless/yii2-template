<?php

/* @var $this yii\web\View */
/* @var $data \common\models\Group */

?>

<?php if ($listDatas) { ?>
    <?php foreach ($listDatas as $data) { ?>
        <div class="order-list-item">
            <div class="order-body<?php if ($data->isOver()) { ?> order-success<?php } ?><?php if ($data->isCancel()) { ?> order-failure<?php } ?> clearfix">
                <div class="order-preview">
                    <?php if ($data->one_delivery) { ?>
                        <span class="sign sign-helpme">帮我拼</span>
                    <?php } else { ?>
                        <span class="sign sign-letgo">一起拼</span>
                    <?php } ?>
                    <img src="<?= $data->goods->getPreview(150, 150) ?>"/>
                </div>

                <div class="order-info">
                    <p><?= $data->goods->name ?></p>
                    <?php if ($data->isOver() || $data->isCancel()) { ?>
                        <p><span class="red"><?= $data->quantity ?></span> 人拼</p>
                    <?php } else { ?>
                        <p><span class="red"><?= $data->quantity ?></span> 人拼，<?= $data->showStatus() ?></p>
                    <?php } ?>
                    <?php if ($data->status == \common\models\Group::STATUS_ACTIVE && $data->quantity > $data->joiner) { ?>
                        <p>还差 <span class="red"><?= $data->quantity - $data->joiner ?></span> 人帮忙</p>
                    <?php } ?>
                    <p>开拼时间：<?= date("Y-m-d H:i:s", $data->created_at) ?></p>
                </div>

                <div class="order-action">
                    <div class="pull-left">
                        拼单 #<?= $data->id ?>
                    </div>
                    <?php if ($data->status == \common\models\Group::STATUS_ACTIVE) { ?>
                        <a class="btn btn-primary" href="<?= $data->getGroupShareUrl() ?>">去拼单</a>
                    <?php } ?>
                    <a class="btn btn-default" href="<?= $data->getGroupUrl() ?>">查看</a>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>