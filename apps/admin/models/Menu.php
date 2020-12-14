<?php

namespace admin\models;

use ijony\helpers\Utils;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * 后台菜单数据模型
 *
 * {@inheritdoc}
 */
class Menu extends \common\models\Menu
{

    private static $_routes;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['child', 'parent_id', 'sort'], 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    public function beforeSave($insert)
    {
        if(!$insert){
            if(count(explode(",", $this->child_arr)) > 1){
                $this->child = 1;
            }else{
                $this->child = 0;
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert){
            $this->child_arr = (string) $this->id;

            if($this->parent_id){
                $parent = static::findOne($this->parent_id);

                $parent->child_arr = $parent->child_arr . ',' . $this->id;
                $parent->save();

                $this->parent_arr = $parent->parent_arr . ',' . $this->parent_id;

                $parents = explode(",", $parent->parent_arr);
                foreach($parents as $parent_id){
                    if(!$parent_id) continue;

                    $parent = static::findOne($parent_id);
                    $parent->child_arr = $parent->child_arr . ',' . $this->id;
                    $parent->save();
                }
            }

            $this->save();
        }else{
            if(isset($changedAttributes['parent_id'])){
                $child_arr = explode(",", $this->child_arr);
                $old_parent_arr = $this->parent_arr;

                if($changedAttributes['parent_id']){
                    $parent_arr = explode(",", $this->parent_arr);
                    array_shift($parent_arr);

                    foreach($parent_arr as $parent_id){
                        $parent = static::findOne($parent_id);

                        $parent_child_arr = explode(",", $parent->child_arr);
                        $parent_child_arr = array_diff($parent_child_arr, $child_arr);
                        $parent_child_arr = join(",", $parent_child_arr);

                        $parent->child_arr = $parent_child_arr;
                        $parent->save();
                    }
                }

                if($this->parent_id){
                    $parent = static::findOne($this->parent_id);

                    $parent_child_arr = explode(",", $parent->child_arr);
                    $parent_child_arr = array_merge($parent_child_arr, $child_arr);
                    $parent_child_arr = join(",", $parent_child_arr);

                    $parent->child_arr = $parent_child_arr;
                    $parent->save();

                    $this->parent_arr = $parent->parent_arr . ',' . $this->parent_id;

                    $parents = explode(",", $parent->parent_arr);
                    foreach($parents as $parent_id){
                        if(!$parent_id) continue;

                        $parent = static::findOne($parent_id);

                        $parent_child_arr = explode(",", $parent->child_arr);
                        $parent_child_arr = array_merge($parent_child_arr, $child_arr);
                        $parent_child_arr = join(",", $parent_child_arr);

                        $parent->child_arr = $parent_child_arr;
                        $parent->save();
                    }
                }else{
                    $this->parent_arr = '0';
                }

                foreach($child_arr as $id){
                    if($id == $this->id) continue;

                    $child = static::findOne($id);
                    $child->parent_arr = str_replace($old_parent_arr, $this->parent_arr, $child->parent_arr);
                    $child->save();
                }

                $this->save();
            }

            if(isset($changedAttributes['status'])){
                if($this->child != 0){
                    static::updateAll(['status' => $this->status], ['parent_id' => $this->id]);
                }
            }
        }

        if(isset($changedAttributes['sort'])){
            if($this->parent_id){
                $parent = static::findOne($this->parent_id);
                $child_arr = $parent->id;
                $childs = static::find()->where(['parent_id' => $this->parent_id])->orderBy(['sort' => SORT_ASC])->all();
                foreach($childs as $child){
                    $child_arr .= "," . $child->child_arr;
                }
                $parent->child_arr = $child_arr;
                $parent->save();
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $parent_arr = explode(",", $this->parent_arr);
        $child_arr = explode(",", $this->child_arr);

        array_shift($parent_arr);

        foreach($parent_arr as $parent_id){
            $parent = static::findOne($parent_id);

            $parent_child_arr = explode(",", $parent->child_arr);
            $parent_child_arr = array_diff($parent_child_arr, $child_arr);
            $parent_child_arr = join(",", $parent_child_arr);

            $parent->child_arr = $parent_child_arr;
            $parent->save();
        }

        static::deleteAll(['id' => $child_arr]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmenu()
    {
        return $this->hasMany(Menu::className(), ['parent_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    public function getUrl()
    {
        if(substr($this->params, 0, 4) == 'http'){
            return $this->params;
        }

        if(!$this->controller){
            return 'javascript:void(0)';
        }

        $params[] = sprintf("%s/%s", $this->controller, $this->action);
        if($this->params){
            $params = array_merge($params, parse_str($this->params));
        }

        return Url::to($params);
    }

    public function getParentIds()
    {
        if(!$this->parent_arr){
            return [0];
        }

        $parent_arr = explode(",", $this->parent_arr);

        return $parent_arr;
    }

    public static function getSelectData($parent_id, $exclude = [])
    {
        $query = static::find()->where([
            'parent_id' => $parent_id,
            'status' => static::STATUS_ACTIVE,
        ]);

        if($exclude){
            if(is_array($exclude)){
                $exclude = explode(",", $exclude);
            }
            $query->andFilterWhere(['not in', 'id', (array) $exclude]);
        }

        $items = $query->all();

        if(!$items){
            return [];
        }

        $datas = [
            $parent_id => '请选择',
        ];

        foreach($items as $item){
            $datas[$item->id] = $item->name;
        }

        return $datas;
    }

    public static function getMenus($route)
    {
        /* @var $items \common\models\Menu[] */
        $datas = static::find()->where(['status' => static::STATUS_ACTIVE])->indexBy('id')->orderBy(['sort' => SORT_ASC])->all();
        $return  = [];
        foreach($datas as $data){
            if($data->parent_id == 0){
                $return[] = self::parseMenu($route, $data, $datas);
            }
        }
        return $return;
    }

    /**
     * @param $route    string
     * @param $data     \common\models\Menu
     * @param $datas    \common\models\Menu[]
     *
     * @return array
     */
    private static function parseMenu($route, $data, $datas)
    {
        $menu = [
            'name' => $data->name,
            'url' => $data->getUrl(),
            'active' => $data->getIsActive($route),
            'icon' => $data->icon,
            'show' => $data->getIsShow(),
            'items' => [],
        ];

        if($data->child == 1){
            $child_arr = explode(",", $data->child_arr);
            $child_count = count($child_arr);

            for($i = 0; $i < $child_count; $i++){
                if(!isset($datas[$child_arr[$i]])) continue;

                $submenu = $datas[$child_arr[$i]];

                if($submenu->parent_id == $data->id){
                    $submenu = self::parseMenu($route, $submenu, $datas);

                    if($submenu['active'] == true){
                        $menu['active'] = true;
                    }
                    if($submenu['show'] == true){
                        $menu['show'] = true;
                    }
                    $menu['items'][] = $submenu;
                }
            }
        }

        if($menu['items']){
            $menu['url'] = 'javascript:void(0)';
        }

        return $menu;
    }

    public function getIsActive($route)
    {
        $routeArr = explode("/", $route);
        $action = array_pop($routeArr);
        $controller = join("/", $routeArr);

        if($this->auth_item){
            $auth_item = explode(",", $this->auth_item);
            return in_array($controller, $auth_item) || in_array($route, $auth_item);
        }

        if($this->parent_id == 0){
            return $this->controller == $controller;
        }

        if($this->action){
            return $this->controller . '/' . $this->action == $route;
        }

        return $this->controller == $controller;
    }

    public function getIsShow()
    {
        $routes = self::getAuthedRoute();

        if(!$routes){
            return true;
        }

        $routes['site'] = '*';

        if($this->auth_item){
            $auth_item = explode(",", $this->auth_item);
            foreach($auth_item as $item){
                if(isset($routes[$item])){
                    return true;
                }
            }
        }

        if(!isset($routes[$this->controller])){
            return false;
        }

        if($routes[$this->controller] == '*'){
            return true;
        }

        $action = $this->action;

        if(!$action){
            $action = 'view';
        }

        if(in_array($action, $routes[$this->controller])){
            return true;
        }

        return false;
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

    public static function getAuthedRoute()
    {
        if(self::$_routes === null){
            self::$_routes = Yii::$app->controller->authed_route;
        }

        return self::$_routes;
    }
}
