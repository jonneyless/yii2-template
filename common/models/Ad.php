<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * {@inheritdoc}
 */
class Ad extends namespace\base\Ad
{

    const TYPE_FOCUS = 0;
    const TYPE_GUIDE = 1;

    const MODE_CATEGORY = 'category';
    const MODE_STORE = 'store';
    const MODE_GOODS = 'goods';
    const MODE_URL = 'url';

    const STATUS_UNACTIVE = 0;    // 禁用
    const STATUS_ACTIVE = 9;      // 启用
}
