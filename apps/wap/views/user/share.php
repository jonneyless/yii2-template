<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = '来就省邀请码';
?>

<div class="share">
    <div class="qrcode">
        <img src="<?= $qrcode ?>" />
    </div>

    <div class="button">
        <?= Html::tag('span', '邀请好友赚奖励', ['class' => 'btn']) ?>
    </div>
</div>
