<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = '下载客户端';
?>

<div class="share share-noqrcode">
    <div class="qrcode">
        <img class="empty" src="/img/empty.gif" />
    </div>

    <div class="button">
        <div class="row">
            <?= Html::a('安卓版', Yii::$app->params['app']['android'], ['class' => 'btn']) ?>
            <?= Html::a('苹果版', Yii::$app->params['app']['ios'], ['class' => 'btn']) ?>
        </div>
    </div>
</div>
