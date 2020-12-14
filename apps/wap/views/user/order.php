<?php

use common\models\Order;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $data \common\models\Order */

$this->title = '我的订单';

?>

    <div class="order-list-nav">
        <a<?php if ($status == '') { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/order']) ?>">全部订单</a>
        <a<?php if ($status == Order::STATUS_NEW) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/order', 'status' => Order::STATUS_NEW]) ?>">待支付</a>
        <a<?php if ($status == Order::STATUS_DELIVERY) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/order', 'status' => Order::STATUS_DELIVERY]) ?>">待收货</a>
    </div>

    <div class="order-list">
        <?php if ($listDatas) { ?>
            <?= $this->context->renderPartial('order-list', ['listDatas' => $listDatas]) ?>

            <div class="pager">
                <?= LinkPager::widget([
                    'pagination' => $listPages,
                ]) ?>
            </div>
        <?php } else { ?>
            <div class="list-empty">
                <span class="glyphicon glyphicon-info-sign"></span><br/>
                <?php
                if ($status == '') {
                    echo '尚无订单<br />请点击其它菜单进行查询';
                } else if ($status == Order::STATUS_NEW) {
                    echo '尚无待支付的订单<br />请点击其它菜单进行查询';
                } else if ($status == Order::STATUS_DELIVERY) {
                    echo '尚无待收货的订单<br />请点击其它菜单进行查询';
                }
                ?>
            </div>
        <?php } ?>
    </div>

<?php

$js = <<<JS

var scrollTop = 0;
var loading = 0;
var isBotton = 0;
var needMore = 0;
var searchBox = $('#search-box');
var useSearch = false;

if(searchBox.length > 0){
    useSearch = true;
}

if(useSearch){
    var searchBoxStatus = searchBox.hasClass('search-box-fix');
    var searchBoxTop = searchBox.offset().top;
}

var footerTop = $('.footer').offset().top;
var windowsHight = $(window).height();
var nextUrl = '';

if(footerTop > windowsHight){
    needMore = 1;
}

$(window).scroll(function(){
    scrollTop = $(window).scrollTop();
    
    if(useSearch){
        if(searchBoxStatus && scrollTop < searchBoxTop){
            searchBox.removeClass('search-box-fix');
            searchBoxStatus = false;
        }else if(!searchBoxStatus && scrollTop >= searchBoxTop){
            searchBox.addClass('search-box-fix');
            searchBoxStatus = true;
        }
    }
    
    if(needMore == 1 && loading == 0 && isBotton == 0 && scrollTop + windowsHight >= footerTop){
        if($('.pager .next a').length == 0){
            isBotton = 1;
            $('.footer').text('已经没有更多内容了...');
            return;
        }
        
        loading = 1;
        nextUrl = $('.pager .next a').attr('href');
        
        $.get(nextUrl, function(datas){
            $('.pager').before(datas.list);
            $('.pager').html(datas.page);
            
            loading = 0;
            footerTop = $('.footer').offset().top - 60;
        }, 'json')
    }
});

JS;

$this->registerJs($js);
?>