<?php

namespace wap\controllers;

use common\models\User;
use libs\Utils;
use libs\Wechat;
use Yii;
use yii\helpers\Url;

/**
 * 公用控制器方法
 *
 * @property \common\models\User $user
 *
 * @package wap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class Controller extends \yii\web\Controller
{

    public $header = true;
    public $bottomBar = [
        ['label' => '我的订单', 'url' => ['user/order'], 'icon' => '&#xe653;', 'class' => '', 'options' => []],
        ['label' => '活动介绍', 'url' => ['site/about'], 'icon' => '&#xe666;', 'class' => '', 'options' => []],
    ];

    public $backUrl = 'javascript:history.go(-1)';

    public $bottomBarActive = '';

    public function init()
    {
        parent::init();

        $PLATFORM = strtolower(Yii::$app->request->get('PLATFORM', ''));
        $BANKPAY = strtolower(Yii::$app->request->get('BANKPAY', ''));

        if ($PLATFORM && $BANKPAY == 'ccb') {
            Yii::$app->session->set('WEB_VIEW', $PLATFORM == 'iphone' ? "1" : "2");
        } else if (isset($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT']) && substr($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT'], 0, 10) == 'CCBWebView') {
            $agent = strtolower($_SERVER['HTTP_CCBWEBVIEW_USER_AGENT']);
            $iphone = (strpos($agent, 'iphone')) ? true : false;
            $ipad = (strpos($agent, 'ipad')) ? true : false;
            $android = (strpos($agent, 'android')) ? true : false;
            Yii::$app->session->set('WEB_VIEW', ($iphone || $ipad) ? "1" : "2");
        } else {
            Yii::$app->session->set('WEB_VIEW', 0);
        }
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
        return $this->render('/site/error', [
            'message' => $message,
            'url' => $url,
            'delay' => $delay,
        ]);
    }
}
