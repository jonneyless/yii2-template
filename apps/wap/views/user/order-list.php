<?php

use wap\assets\PageAsset;

/* @var $this yii\web\View */
/* @var $data \common\models\Order */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

?>

<?php if ($listDatas) { ?>
    <?php foreach ($listDatas as $data) { ?>
        <div class="order-list-item">
            <div class="order-bar clearfix">
                <div class="pull-left">订单编号：<?= $data->id ?></div>
                <div class="pull-right">拼单 #<?= $data->group_id ?></div>
            </div>

            <div class="order-body clearfix">
                <div class="order-preview">
                    <?php if ($data->group->one_delivery) { ?>
                        <span class="sign sign-helpme">帮我拼</span>
                    <?php } else { ?>
                        <span class="sign sign-letgo">一起拼</span>
                    <?php } ?>
                    <img src="<?= $data->goods->getPreview(150, 150) ?>"/>
                </div>

                <div class="order-info">
                    <p><?= $data->goods->name ?></p>
                    <p>订单金额：<span class="red">￥<?= $data->amount ?></span></p>
                    <p>订单状态：<?= $data->showStatus() ?></p>
                    <p>下单时间：<?= date("Y-m-d H:i:s", $data->created_at) ?></p>
                </div>

                <div class="order-action">
                    <div class="order-role">
                        <?php if ($data->user_id == $data->group->user_id) { ?>
                            发起人
                        <?php } else { ?>
                            参与人
                        <?php } ?>
                    </div>
                    <?php if ($data->status == \common\models\Order::STATUS_NEW) { ?>
                        <p>
                            <span class="glyphicon glyphicon-exclamation-sign"></span> 订单有效期<?= Yii::$app->params['order.pay.expire']; ?>分钟，请在有效期内及时付款。
                        </p>
                        <a class="btn btn-primary" href="<?= $data->getPayUrl() ?>">去支付</a>
                        <a class="btn btn-success" href="<?= $data->getCancelUrl() ?>" need-confirm="确定要取消订单嘛？">取消</a>
                    <?php } ?>
                    <a class="btn btn-default" href="<?= $data->getOrderUrl() ?>">查看</a>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php

$js = <<<JS
    
    $('a[need-confirm]').click(function(){
        var msg = $(this).attr('need-confirm');
        
        if(confirm(msg)){
            return true;
        }
        
        return false;
    });
    
JS;

