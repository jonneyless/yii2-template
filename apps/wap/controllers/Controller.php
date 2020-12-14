<?php

namespace wap\controllers;

use common\models\Region;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 公用控制器方法
 *
 * @property \common\models\Region $region
 *
 * @package mtwap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class Controller extends \yii\web\Controller
{
    public $bottomBar;
    public $topBar = [];

    public $inApp = false;

    public $backUrl = 'javascript:history.go(-1)';

    public $bottomBarActive = '';

    public function init()
    {
        parent::init();

        $inApp = Yii::$app->request->getQueryParam('in-app', false);
        $token = Yii::$app->request->getQueryParam('token');
        $token = Yii::$app->request->getQueryParam('access-token', $token);

        if(Yii::$app->wechat->getIsWechat()){
            $this->topBar = false;
        }

        if($token){
            Yii::$app->user->loginByAccessToken($token);
        }

        $this->setBottons();

        if($inApp){
            $this->bottomBar = false;
            $this->topBar = false;
        }
    }

    public function setBottons()
    {
        $this->bottomBar = [
            ['label' => '分类', 'url' => ['category/index'], 'icon' => 'category', 'class' => '', 'options' => []],
            ['label' => '购物车', 'url' => ['cart/index'], 'icon' => 'cart', 'class' => '', 'options' => []],
            ['label' => '我的', 'url' => ['user/index'], 'icon' => 'user', 'class' => '', 'options' => []],
        ];
    }

    /**
     * 提示跳转页
     *
     * @param        $message
     * @param string $url
     * @param int    $delay
     *
     * @return string
     */
    public function message($message, $url = 'javascript:history.go(-1)', $delay = 3)
    {
        $params = [
            'message' => '',
            'url' => $url,
            'delay' => $delay,
        ];

        if(is_array($message)){
            $params = ArrayHelper::merge($params, $message);
        }else{
            $params['message'] = $message;
        }

        return $this->render('/site/error', $params);
    }
}
