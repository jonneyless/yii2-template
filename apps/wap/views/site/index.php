<?php

use wap\assets\PageAsset;
use libs\Utils;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $listDatas \common\models\Goods */

PageAsset::register($this)->init([
    'js' => [
        'js/swipe.js',
    ],
]);

$this->title = '广西特网';
?>

<div class="goods-hot">
    <span>热销推荐</span>
</div>

<div class="goods-list">
    <?= $this->context->renderPartial('goods-list', ['listDatas' => $listDatas]) ?>
</div>