<?php

/* @var $this yii\web\View */

?>

<?php if($bottons !== false){ ?>
<div class="topbar">
    <div class="top-left">
        <a href="javascript:;"><i class="fc fc-menu"></i></a>
    </div>

    <div class="top-middle">
        <form>
            <input type="text" name="keyword" placeholder="输入商品名称搜索" />
        </form>
    </div>

    <div class="top-right">
        <a href="javascript:;"><i class="fc fc-message"></i></a>
    </div>
</div>
<?php } ?>