<?php

namespace admin\models;

use ijony\helpers\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 管理员角色数据模型
 *
 * {@inheritdoc}
 *
 * @property array $authArr write-only
 * @property array $routeArr write-only
 */
class AdminRole extends \common\models\AdminRole
{

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
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->authArr = $this->auth ? explode(",", $this->auth) : [];
        $this->routeArr = $this->route ? explode(",", $this->route) : [];
    }

    public function parseAuth()
    {
        if($this->auth){
            $routes = AdminAuth::find()->select('route')->where([
                'or',
                ['in', 'key', $this->auth],
                ['in', 'parent', $this->auth],
            ])->column();

            $routes = join(",", $routes);
            $routes = explode(",", $routes);
            $routes = array_unique($routes);

            $forAll = [];
            foreach($routes as $route){
                $route = explode("/", $route);
                $action = array_pop($route);
                $controller = join("/", $route);

                if($action == '*'){
                    $forAll[$controller] = $controller;
                }
            }

            foreach($routes as $index => $route){
                $route = explode("/", $route);
                $action = array_pop($route);
                $controller = join("/", $route);

                if(isset($forAll[$controller]) && $action != '*'){
                    unset($routes[$index]);
                }
            }

            $this->route = join(",", $routes);
            $this->auth = join(",", $this->auth);
        }else{
            $this->route = '';
            $this->auth = '';
        }

        return true;
    }

    public function getAuth()
    {
        return $this->authArr;
    }

    public function getRoute()
    {
        $datas = [];

        foreach($this->routeArr as $route){
            $route = explode("/", $route);
            $action = array_pop($route);
            $controller = join("/", $route);

            if($action == "*"){
                $datas[$controller] = $action;
            }else{
                if(isset($datas[$controller]) && $datas[$controller] == '*'){
                    continue;
                }

                $datas[$controller][] = $action;
            }
        }

        return $datas;
    }

    public function getStatus()
    {
        $datas = $this->getStatusSelectData();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    public function getStatusLabel()
    {
        if($this->status == self::STATUS_ACTIVE){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getStatus(), $class);
    }

    public function getStatusSelectData()
    {
        return [
            self::STATUS_UNACTIVE => '禁用',
            self::STATUS_ACTIVE => '启用',
        ];
    }
}
