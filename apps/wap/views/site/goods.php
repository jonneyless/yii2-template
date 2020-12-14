<?php

use wap\assets\PageAsset;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Goods */
/* @var $gallery \common\models\GoodsGallery */
/* @var $attr \common\models\GoodsAttribute */

PageAsset::register($this)->init([
    'js' => [
        'js/swipe.js',
    ],
]);

$this->context->bottomBar = [];
$this->context->bottomBar[] = [
    'label' => '￥' . $model->price . '<br />五斤装',
    'url' => $model->getCartUrl(1),
    'class' => 'buy',
];

$this->title = '商品详情';
?>

    <div class="swipe-box">
        <ul class="list-unstyled">
            <li><img src="<?= $model->getPreview() ?>"/></li>
            <?php foreach ($model->gallery as $gallery) { ?>
                <li><img src="<?= $gallery->getImage() ?>"/></li>
            <?php } ?>
        </ul>
        <ol>
            <li class="on"></li>
            <?php foreach ($model->gallery as $gallery) { ?>
                <li></li>
            <?php } ?>
        </ol>
    </div>

    <div class="goods-detail">
        <h4><?= $model->name ?></h4>
        <p class="gray"><?= $model->description ?></p>
        <div class="gray">
            已售：<?= $model->sales ?> 件
        </div>
    </div>

    <div class="tabs-box">
        <ul class="tabs-nav">
            <li class="active">商品详情</li>
            <li>商品属性</li>
        </ul>

        <div class="tabs-body">
            <?= $model->content ?>
        </div>

        <div class="tabs-body" style="display: none">
            <ul class="list-unstyled">
                <?php foreach ($model->attrs as $attr) { ?>
                    <li>
                        <label><?= $attr->name ?>：</label>
                        <?= $attr->value ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
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
            <li>
                <span>2</span>
                截图分享<br>邀请好友
            </li>
            <li>
                <span>3</span>
                多名好友<br>完成支付
            </li>
            <li>
                <span>4</span>
                满足条件<br>拼团成功
            </li>
        </ul>
    </div>

    <div class="bottom-bar">
        <ul>
            <li>
                <a href="<?= Yii::$app->homeUrl ?>">
                    <span class="icon iconfont">&#xe61d;</span><br/>
                    首页
                </a>
            </li>
            <li>
                <a href="<?= $model->getCartUrl(1) ?>">
                    ￥<?= $model->price ?><br/>
                    拼单购买
                </a>
            </li>
        </ul>
    </div>

<?php

$js = <<<JS

new Swipe($('.swipe-box')[0], {
    speed: 500,
    auto: 3000,
    callback: function(){
        var lis = $(this.element).next("ol").children();
        lis.removeClass("on").eq(this.index).addClass("on");
    }
});

$('.tabs-nav li').click(function(){
    $(this).closest('.tabs-box').find('.tabs-body').hide();
    $(this).closest('.tabs-nav').find('li').removeClass('active');
    $(this).closest('.tabs-box').find('.tabs-body').eq($(this).prevAll('li').length).show();
    $(this).addClass('active');
});
		
JS;

$this->registerJs($js);
