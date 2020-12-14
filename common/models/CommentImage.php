<?php

namespace common\models;

use ijony\helpers\File;
use Yii;

/**
 * This is the model class for table "{{%comment_image}}".
 *
 * {@inheritdoc}
 */
class CommentImage extends namespace\base\CommentImage
{

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        File::delFile($this->image, true);
    }
}
