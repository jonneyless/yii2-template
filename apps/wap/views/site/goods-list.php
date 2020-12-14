<?php

/* @var $this yii\web\View */
/* @var $listDatas \common\models\Goods[] */

?>

<?php foreach ($listDatas as $data) { ?>
    <a class="goods-item" href="<?= $data->getViewUrl() ?>">
        <div class="goods-preview">
            <img src="<?= $data->getPreview(150, 150) ?>"/>
        </div>

        <div class="goods-info">
            <h4><?= $data->name ?></h4>
            <div class="prices">
                <div>
                    特价<br/>
                    <strong class="red">￥<?= $data->price ?></strong>
                </div>
            </div>
            已售<strong class="red"><?= $data->sales ?></strong>件 &nbsp; &nbsp; <span class="go-group">去下单</span>
        </div>
    </a>
<?php } ?>