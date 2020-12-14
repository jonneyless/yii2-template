<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use Yii;

/**
 * This is the model class for table "{{%goods_mode}}".
 *
 * {@inheritdoc}
 */
class GoodsMode extends \common\models\GoodsMode
{

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['image']) && $changedAttributes['image']){
            File::delFile($changedAttributes['image'], true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        if($this->image){
            File::delFile($this->image, true);
        }
    }

    public function getImage()
    {
        return Image::getImg($this->image, 200, 100);
    }
}
