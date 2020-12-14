<?php

namespace wap\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
        'css/common.css',
        'css/iconfont/iconfont.css',
        'css/mobile-select-area.css',
    ];
    public $js = [
        'js/common.js',
        'js/dialog.js',
        'js/mobile-select-area.js',
        'js/layer.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
