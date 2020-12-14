<?php

use wap\assets\PageAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \common\models\Address */
/* @var $form yii\widgets\ActiveForm */

PageAsset::register($this)->init([
    'js' => [
        'js/layer/layer.js',
    ],
]);

$this->title = '收货地址管理';
?>

<div class="box">
    <ul class="address-list">
        <?php foreach ($listDatas as $data) { ?>
            <li>
                <a href="<?= Url::to(['user/address', 'id' => $data->id]) ?>">
                    <?= $data->consignee ?>，<?= $data->phone ?><br/>
                    <?= $data->showAreaLine() ?> <?= $data->address ?>
                </a>
            </li>
        <?php } ?>
    </ul>

    <a class="btn btn-primary form-control" href="<?= Url::to(['user/create-address']) ?>">新建收货地址</a>
</div>
