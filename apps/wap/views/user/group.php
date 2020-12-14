<?php

use common\models\Group;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $data \common\models\Group */

$this->title = '我的拼单';

?>

    <div class="order-list-nav">
        <a<?php if ($status == Group::STATUS_OVER) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/group', 'status' => Group::STATUS_OVER]) ?>">拼单成功</a>
        <a<?php if ($status == Group::STATUS_ACTIVE) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/group', 'status' => Group::STATUS_ACTIVE]) ?>">拼单中</a>
        <a<?php if ($status == Group::STATUS_CANCEL) { ?> class="active"<?php } ?> rel="holder-link" href="<?= Url::to(['user/group', 'status' => Group::STATUS_CANCEL]) ?>">拼单失败</a>
    </div>

    <div class="order-list">
        <?php if ($listDatas) { ?>
            <?= $this->context->renderPartial('group-list', ['listDatas' => $listDatas]) ?>

            <div class="pager">
                <?= LinkPager::widget([
                    'pagination' => $listPages,
                ]) ?>
            </div>
        <?php } else { ?>
            <div class="list-empty">
                <span class="glyphicon glyphicon-info-sign"></span><br/>
                <?php
                if ($status == Group::STATUS_OVER) {
                    echo '尚无成功的拼单<br />请点击其它菜单进行查询';
                } else if ($status == Group::STATUS_ACTIVE) {
                    echo '尚无进行中的拼单<br />请点击其它菜单进行查询';
                } else if ($status == Group::STATUS_CANCEL) {
                    echo '尚无失败的拼单<br />请点击其它菜单进行查询';
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