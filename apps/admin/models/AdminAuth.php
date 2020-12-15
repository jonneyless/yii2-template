<?php

namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * {@inheritdoc}
 *
 * @property AdminAuth[] $childAuth
 * @property AdminAuth $parentAuth
 */
class AdminAuth extends \common\models\AdminAuth
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildAuth()
    {
        return $this->hasMany(AdminAuth::className(), ['parent' => 'key']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentAuth()
    {
        return $this->hasOne(AdminAuth::className(), ['key' => 'parent']);
    }

    /**
     * @return string
     */
    public function getParentName()
    {
        return $this->parentAuth ? $this->parentAuth->name : '';
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return join("<br>", explode(",", $this->route));
    }

    /**
     * @param $route
     */
    public function addRoute($route)
    {
        $routes = [];

        if ($this->route) {
            $routes = explode(",", $this->route);
        }

        $routes[] = $route;

        $routes = array_unique($routes);

        sort($routes);

        $this->route = join(",", $routes);
    }

    /**
     * @param $datas
     */
    public function addRoutes($datas)
    {
        $routes = [];

        if ($this->route) {
            $routes = explode(",", $this->route);
        }

        $routes = ArrayHelper::merge($routes, $datas);

        $routes = array_unique($routes);

        sort($routes);

        $this->route = join(",", $routes);
    }
}
