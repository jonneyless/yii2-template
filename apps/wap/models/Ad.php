<?php

namespace wap\models;

use ijony\helpers\Image;
use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * {@inheritdoc}
 */
class Ad extends \common\models\Ad
{

    public function getImage($width = 0, $height = 0)
    {
        return Image::getImg($this->image, $width, $height);
    }

    public function getJumpUrl()
    {
        return [$this->mode . '/index', 'id' => $this->url];
    }
}
