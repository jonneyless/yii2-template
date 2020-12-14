<?php
/**
 * @var $data \wap\models\Goods[]
 */
?>

<?php foreach($data as $datum){ ?>
<li>
    <a href="<?= $datum->getViewUrl() ?>"><img src="<?= $datum->showPreview(340, 340) ?>" alt="<?= $datum->name ?>" /></a>
    <h3><?= $datum->showName(18) ?></h3>
    <p><span class="original_price">￥<?= $datum->original_price ?></span></p>
    <p>会员价：<span class="member_price">￥<?= $datum->member_price ?></span></p>
</li>
<?php } ?>