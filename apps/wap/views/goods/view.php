<?php

/* @var $this yii\web\View */
/* @var $model wap\models\Goods */

$this->title = $model->name;
?>

<div class="single-page">
    <?= $model->showContent() ?>
</div>

