<?php

namespace admin\controllers;

use admin\assets\AppAsset;
use admin\models\Menu;
use Yii;
use yii\helpers\Url;
use function common\libs\user;

/**
 * 控制器基类
 *
 * {@inheritdoc}
 */
class BaseController extends \ijony\admin\controllers\Controller
{

    public $authed_route = [];
    public $authed_auth = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (YII_ENV_DEV) {
            $this->topButtons[] = [
                'name' => '菜单',
                'url' => Url::to(['/menu']),
                'icon' => 'list',
            ];
        }

        if (!user()->getIsGuest()) {
            /** @var $user \admin\models\Admin */
            $user = user()->identity;

            if ($user->role) {
                $this->authed_route = $user->role->getRoute();
                $this->authed_auth = $user->role->getAuth();
            }
        }

        AppAsset::register($this->view);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->view->params['route'] = $action->controller->id . '/' . $action->id;

            return true;
        }

        return false;
    }

    /**
     * 按钮授权
     *
     * @param $button
     *
     * @return bool
     */
    public function checkButton($button)
    {
        if (isset($button['show'])) {
            return $button['show'];
        }

        if (isset($button['auth'])) {
            return $this->checkAuth($button['auth']);
        }

        return $this->checkRoute($button['url']);
    }

    /**
     * 授权检测
     *
     * @param $auth
     *
     * @return bool
     */
    public function checkAuth($auth)
    {
        if (!$this->authed_auth) {
            return true;
        }

        if (in_array($auth, $this->authed_auth)) {
            return true;
        }

        return $this->checkRoute($auth);
    }

    /**
     * 路由授权验证
     *
     * @param $route
     *
     * @return bool
     */
    public function checkRoute($route)
    {
        if (!$this->authed_route) {
            return true;
        }

        if (is_string($route)) {
            $route = explode("/", $route);
        }

        $controller = '';
        $action = '';

        if (!$route[0]) {
            if (isset($route[1])) {
                $controller = $route[1];
            }

            if (isset($route[2])) {
                $action = $route[2];
            }
        } else {
            $controller = isset($route[1]) ? $route[0] : $this->id;
            $action = isset($route[1]) ? $route[1] : $route[0];
        }

        if (isset($this->authed_route[$controller])) {
            if ($this->authed_route[$controller] == '*') {
                return true;
            }

            if ($action && in_array($action, $this->authed_route[$controller])) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenus()
    {
        return Menu::getMenus($this->view->params['route']);
    }

    /**
     * 生成授权规则
     *
     * @param       $controller
     * @param array $rules
     *
     * @return array
     */
    public function getRules($controller, $rules = [])
    {
        if (!$this->authed_route) {
            $rules[] = [
                'allow' => true,
                'roles' => ['@'],
            ];
        } else {
            if (isset($this->authed_route[$controller])) {
                if ($this->authed_route[$controller] == '*') {
                    $rules[] = [
                        'allow' => true,
                        'roles' => ['@'],
                    ];
                } else {
                    $rules[] = [
                        'actions' => $this->authed_route[$controller],
                        'allow' => true,
                        'roles' => ['@'],
                    ];
                }
            } else {
                $rules[] = [
                    'allow' => false,
                ];
            }
        }

        return $rules;
    }
}
