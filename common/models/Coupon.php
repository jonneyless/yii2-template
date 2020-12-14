<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%coupon}}".
 *
 * {@inheritdoc}
 */
class Coupon extends \common\models\base\Coupon
{

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 9;
}
