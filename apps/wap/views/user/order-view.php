<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Order */
/* @var $virtual \common\models\GoodsVirtual */

$this->title = '订单详情';
?>

    <div class="order-list-item">
        <div class="order-bar clearfix">
            <div class="pull-left"><?= date("Y-m-d H:i:s", $model->created_at) ?></div>
            <div class="pull-right">拼单 #<?= $model->group_id ?></div>
        </div>

        <div class="order-body clearfix">
            <div class="order-preview">
                <?php if ($model->goods->one_delivery) { ?>
                    <span class="sign sign-helpme">帮我拼</span>
                <?php } else { ?>
                    <span class="sign sign-letgo">一起拼</span>
                <?php } ?>
                <img src="<?= $model->goods->getPreview(150, 150) ?>"/>
            </div>

            <div class="order-info">
                <p><?= $model->goods->name ?></p>
                <p>订单编号：<?= $model->id ?></p>
                <p>订单金额：<span class="red">￥<?= $model->amount ?></span></p>
                <p>订单状态：<?= $model->showStatus() ?></p>
            </div>

            <div class="order-action">
                <?php if ($model->payment_status == \common\models\Order::PAYMENT_REFUND) { ?>
                    <p class="red text-center">您的款项将在三个工作日内退还到付款账户</p>
                <?php } ?>

                <?php if ($model->status == \common\models\Order::STATUS_NEW) { ?>
                    <a class="btn btn-primary" href="<?= $model->getPayUrl() ?>">去支付</a>
                <?php } ?>
            </div>
        </div>
    </div>

<?php if ($model->goods->virtual_notice) { ?>
    <div class="box box-nobg">
        <table class="virtual-notice">
            <tr>
                <td class="red" width="64">温馨提醒</td>
                <td><?= $model->goods->virtual_notice ?></td>
            </tr>
        </table>
    </div>
<?php } ?>
<?php if ($model->goods->is_virtual == 0) { // 如果是实体商品 ?>
    <?php if ($model->goods->one_delivery == 1) { // 如果只发团长 ?>
        <?php if ($model->user_id == $model->group->user_id) { ?>
            <div class="box">
                <span class="gray">收货信息：</span><br/>
                收 货 人：<?= $model->group->consignee ?><br/>
                联系电话：<?= $model->group->phone ?>
                <?php if ($model->group->address) { ?>
                    <br/>收货地址：<?= $model->group->showAreaLine() ?> <?= $model->group->address ?>
                <?php } ?>
            </div>
            <?php if ($model->delivery_name && $model->delivery_number) { ?>
                <div class="box">
                    <span class="gray">发货信息：</span><br/>
                    物流名称：<?= $model->delivery_name ?><br/>
                    物流单号：<?= $model->delivery_number ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <div class="box">
            <span class="gray">收货信息：</span><br/>
            收 货 人：<?= $model->consignee ?><br/>
            联系电话：<?= $model->phone ?>
            <?php if ($model->address) { ?>
                <br/>收货地址：<?= $model->showAreaLine() ?> <?= $model->address ?>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { // 如果是虚拟卡 ?>
    <?php if ($model->virtual) { // 当是可显示的 ?>
        <div class="box">
            <?php if (!$model->goods->virtual_notice) { ?>
                <span class="red">凭此券到门店进行消费</span><br/>
                <span class="gray">虚拟卡信息：</span>
            <?php } ?>
            <hr>
            <?php $count = count($model->virtual); ?>
            <?php foreach ($model->virtual as $index => $virtual) { ?>
                <?php if ($count > 1) { ?><?= $index + 1 ?>、<?php } ?>
                <?php if ($virtual->number) { ?>卡号：<?= $virtual->showNumber() ?><br/><?php } ?>
                <?php if ($virtual->code) { ?><?php if ($count > 1 && $virtual->number) { ?>　　<?php } ?>卡密：<?= $virtual->showCode() ?>
                    <br/><?php } ?>
                <?php if ($count > 1) { ?>　　<?php } ?>于 <?= $virtual->end_time; ?> 到期
                <hr>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>