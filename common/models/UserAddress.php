<?php

namespace common\models;

use Yii;

/**
 * 用户收货地址数据模型
 *
 * {@inheritdoc}
 */
class UserAddress extends namespace\base\UserAddress
{

    const IS_DEFAULT_NO = 0;  // 非默认
    const IS_DEFAULT_YES = 9;    // 是默认

}
