<?php

use wap\assets\PageAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $focus \wap\models\Ad[] */
/* @var $guide \wap\models\Ad[] */
/* @var $hots \wap\models\Goods[] */

$this->title = '来就省商城';

PageAsset::register($this)->init([
    'js' => [
        'js/swipe.js',
    ],
]);
?>

<?php if($focus){ ?>
    <div class="swipe-box">
        <ul class="list-unstyled">
            <?php foreach($focus as $item){ ?>
                <li><?= Html::a(Html::img($item->getImage(640, 214)), $item->getJumpUrl()) ?></li>
            <?php } ?>
        </ul>
        <ol>
            <?php foreach($focus as $index => $item){ ?>
                <li<?php if($index == 0){ ?> class="on"<?php } ?>></li>
            <?php } ?>
        </ol>
    </div>
<?php } ?>

<div class="index-guide">
    <ul>
        <?php foreach($guide as $item){ ?>
            <li><?= Html::a(Html::img($item->getImage(), ['alt' => $item['name']]), $item->getJumpUrl(), ['title' => $item['name']]) ?></li>
        <?php } ?>
    </ul>
</div>

<div class="box goods-list hot-goods">
    <div class="box-header">Top 热销商品</div>

    <div class="box-content">
        <ul>
            <?= $this->render('/item/goods', ['data' => $hots], $this->context) ?>
        </ul>
    </div>

    <div class="box-footer"><a href="<?= \yii\helpers\Url::to(['goods/list', 'is_hot' => 1]) ?>">查看全部 >></a></div>
</div>

<div class="box goods-list">
    <div class="box-header">精品推荐</div>

    <div class="box-content">
        <ul id="list-area">
            <?= $this->render('/item/goods', ['data' => $hots], $this->context) ?>
        </ul>
    </div>

    <div class="box-footer"><span id="listBotom">下拉加载更多</span></div>
</div>

<?php

$url = \ijony\helpers\Url::to(['site/index', 'page' => '']);
$js = <<<JS

new Swipe($('.swipe-box')[0], {
    speed: 500,
    auto: 3000,
    callback: function(){
        var lis = $(this.element).next("ol").children();
        lis.removeClass("on").eq(this.index).addClass("on");
    }
});

var url = '$url';
var page = $page;
var nomore = false;
var loading = false;
var scrolled = $(document).scrollTop();
var screenHight = $(window).height();
var listBottom = $('#listBotom').offset().top;

$(document).scroll(function(){
    if(loading || nomore){
        return;
    }
    
    scrolled = $(document).scrollTop();
    
    if(screenHight + scrolled + 100 > listBottom){
        loading = true;
        
        $.get(url + page, function(data){
            if(page != data.page){
                page = data.page;
                
                $('#list-area').append(data.html);
                
                loading = false;
                listBottom = $('#listBotom').offset().top;
            }else{
                nomore = true;
                
                $('#listBotom').text('没有更多商品了！');
            }
        }, 'json');
    }
});

JS;

$this->registerJs($js);