<?php

namespace common\models;

use Yii;

/**
 * 管理员数据模型
 *
 * {@inheritdoc}
 */
class Admin extends namespace\base\Admin
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用

}
