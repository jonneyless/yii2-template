<?php

namespace wap\assets;

use yii\web\AssetBundle;

/**
 * Main wap application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/icon/iconfont.css?20161103',
        'css/common.css?20170328',
        'css/media.css?20161103',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
