<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use wap\assets\AppAsset;
use wap\widgets\BottomBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimum-scale=1, maximum-scale=1"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script>
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?366e919288bc7d2db5064dcd3919de6b";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body<?php if (!$this->context->header) { ?> class="no-header"<?php } ?>>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <a class="icon iconfont" href="<?= $this->context->backUrl ?>">&#xe658;</a>
            </div>
            <div class="header-text"><?= $this->title ?></div>
            <div class="header-right">
                <?php if (!Yii::$app->user->getIsGuest()) { ?>
                    <a class="icon iconfont" href="<?= Url::to(['site/logout']) ?>">&#xe645;</a>
                <?php } else { ?>
                    <a class="icon iconfont" href="<?= Url::to(['site/login']) ?>">&#xe60a;</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="tbody">
        <?= $content ?>
    </div>

    <div class="footer">
        客服电话：400-820-4485
    </div>

    <?= BottomBar::widget() ?>
</div>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
