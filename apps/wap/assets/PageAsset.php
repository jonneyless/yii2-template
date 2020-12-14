<?php

namespace wap\assets;

use yii\web\AssetBundle;

/**
 * 页面静态文件引用
 */
class PageAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'wap\assets\AppAsset',
    ];

    public function init($params = [])
    {
        parent::init();

        if(isset($params['css'])){
            if($this->css){
                $this->css = array_merge($this->css, $params['css']);
                $this->css = array_unique($this->css);
            }else{
                $this->css = $params['css'];
            }
        }

        if(isset($params['js'])){
            if($this->js){
                $this->js = array_merge($this->js, $params['js']);
                $this->js = array_unique($this->js);
            }else{
                $this->js = $params['js'];
            }
        }
    }
}
