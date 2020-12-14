<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = '来就省商城';
?>

<div class="share share-noqrcode">
    <div class="qrcode">
        <img class="empty" src="/img/empty.gif" />
    </div>

    <div class="button">
        <?= Html::a('注册并下载', ['system/promotion', 'id' => $id], ['class' => 'btn']) ?>
    </div>
</div>
