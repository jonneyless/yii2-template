<?php

namespace common\models;

use Yii;

/**
 * 管理员角色数据模型
 *
 * {@inheritdoc}
 */
class AdminRole extends namespace\base\AdminRole
{

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_ACTIVE = 9;    // 启用

}
