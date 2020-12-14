<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property string $id 菜单 ID
 * @property int $parent_id 父级
 * @property string $name 名称
 * @property string $icon 图标
 * @property int $child 有子级
 * @property string $parent_arr 父级链
 * @property string $child_arr 子级群
 * @property string $controller 控制器
 * @property string $action 方法
 * @property string $params 参数
 * @property string $auth_item 权限
 * @property string $sort 排序
 * @property int $status 状态
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'child', 'sort', 'status'], 'integer'],
            [['name'], 'required'],
            [['child_arr'], 'string'],
            [['name', 'icon', 'controller', 'action', 'auth_item'], 'string', 'max' => 30],
            [['parent_arr', 'params'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '菜单 ID',
            'parent_id' => '父级',
            'name' => '名称',
            'icon' => '图标',
            'child' => '有子级',
            'parent_arr' => '父级链',
            'child_arr' => '子级群',
            'controller' => '控制器',
            'action' => '方法',
            'params' => '参数',
            'auth_item' => '权限',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
