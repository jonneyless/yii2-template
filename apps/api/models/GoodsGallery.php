<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;

/**
 * 商品组图数据模型
 *
 * {@inheritdoc}
 */
class GoodsGallery extends \common\models\GoodsGallery
{

    public function fields()
    {
        return [
            'image',
        ];
    }
}
