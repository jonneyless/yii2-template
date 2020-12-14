<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = '报表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-search">
    <div class="form-group">
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/stock']) ?>">商品库存清单</a>
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['report/detail']) ?>">活动结算明细</a>
    </div>
</div>