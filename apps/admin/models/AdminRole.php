<?php

namespace admin\models;

use admin\traits\ModelStatus;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * {@inheritdoc}
 *
 * @property array $authArr write-only
 * @property array $routeArr write-only
 */
class AdminRole extends \common\models\AdminRole
{

    use ModelStatus;

    const STATUS_DELETE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 9;

    public $authArr = [];
    public $routeArr = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['auth', 'route'], 'default', 'value' => ''],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->authArr = $this->auth ? explode(",", $this->auth) : [];
        $this->routeArr = $this->route ? explode(",", $this->route) : [];
    }

    /**
     * @return bool
     */
    public function parseAuth()
    {
        if ($this->authArr) {
            $routes = AdminAuth::find()->select('route')->where([
                'or',
                ['in', 'key', $this->authArr],
                ['in', 'parent', $this->authArr],
            ])->column();

            $routes = join(",", $routes);
            $routes = explode(",", $routes);
            $routes = array_unique($routes);

            $forAll = [];
            foreach ($routes as $route) {
                $route = explode("/", $route);
                $action = array_pop($route);
                $controller = join("/", $route);

                if ($action == '*') {
                    $forAll[$controller] = $controller;
                }
            }

            foreach ($routes as $index => $route) {
                $route = explode("/", $route);
                $action = array_pop($route);
                $controller = join("/", $route);

                if (isset($forAll[$controller]) && $action != '*') {
                    unset($routes[$index]);
                }
            }

            $this->route = join(",", $routes);
        } else {
            $this->route = '';
            $this->auth = '';
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAuth()
    {
        return $this->authArr;
    }

    /**
     * @return array
     */
    public function getRoute()
    {
        $datas = [];

        foreach ($this->routeArr as $route) {
            $route = explode("/", $route);
            $action = array_pop($route);
            $controller = join("/", $route);

            if ($action == "*") {
                $datas[$controller] = $action;
            } else {
                if (isset($datas[$controller]) && $datas[$controller] == '*') {
                    continue;
                }

                $datas[$controller][] = $action;
            }
        }

        return $datas;
    }

    /**
     * @param string $parent
     *
     * @return array|\yii\db\ActiveRecord[]|AdminAuth[]
     */
    public static function getAllAuth($parent = '')
    {
        return AdminAuth::find()->where(['parent' => $parent])->all();
    }
}
