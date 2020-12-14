<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 管理权限数据模型
 *
 * {@inheritdoc}
 */
class AdminAuth extends \common\models\AdminAuth
{

    public function getChildAuth()
    {
        return $this->hasMany(AdminAuth::className(), ['parent' => 'key']);
    }

    public function getParentAuth()
    {
        return $this->hasOne(AdminAuth::className(), ['key' => 'parent']);
    }

    public function getParentName()
    {
        return $this->parentAuth ? $this->parentAuth->name : '';
    }

    public function getRoute()
    {
        return join("<br>", explode(",", $this->route));
    }

    public function addRoute($route)
    {
        $routes = [];

        if($this->route){
            $routes = explode(",", $this->route);
        }

        $routes[] = $route;

        $routes = array_unique($routes);

        sort($routes);

        $this->route = join(",", $routes);
    }

    public function addRoutes($datas)
    {
        $routes = [];

        if($this->route){
            $routes = explode(",", $this->route);
        }

        $routes = ArrayHelper::merge($routes, $datas);

        $routes = array_unique($routes);

        sort($routes);

        $this->route = join(",", $routes);
    }
}
