<?php

use common\models\Ad;
use wap\assets\PageAsset;
use yii\bootstrap\ActiveForm;
use libs\Utils;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $data \common\models\Goods */

$this->title = '爱拼才会赢';
?>

    <div class="order-list-nav">
        <a<?php if ($type == 0) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['site/list', 'type' => 0]) ?>">一起拼</a>
        <a<?php if ($type == 1) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['site/list', 'type' => 1]) ?>">帮我拼</a>
    </div>

    <div class="goods-list">
        <?= $this->context->renderPartial('goods-list', ['listDatas' => $listDatas]) ?>

        <div class="pager">
            <?= LinkPager::widget([
                'pagination' => $listPages,
            ]) ?>
        </div>
    </div>

<?php

$js = <<<JS

var scrollTop = 0;
var loading = 0;
var isBotton = 0;
var needMore = 0;
var searchBox = $('#search-box');
var searchBoxStatus = searchBox.hasClass('search-box-fix');
var searchBoxTop = searchBox.offset().top - 42;
var footerTop = $('.footer').offset().top - 60;
var windowsHight = $(window).height();
var nextUrl = '';

if(footerTop > windowsHight){
    needMore = 1;
}

$(window).scroll(function(){
    scrollTop = $(window).scrollTop();
    
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
