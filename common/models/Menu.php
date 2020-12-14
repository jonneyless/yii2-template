<?php

namespace common\models;

use Yii;

/**
 * 后台菜单数据模型
 *
 * {@inheritdoc}
 */
class Menu extends namespace\base\Menu
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用

}
