<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use Yii;

/**
 * 商品组图数据模型
 *
 * {@inheritdoc}
 */
class GoodsGallery extends \common\models\GoodsGallery
{

    public function beforeSave($insert)
    {
        if($this->image && substr($this->image, 0, 6) == BUFFER_FOLDER){
            $oldImg = $this->image;
            $newImg = Image::copyImg($this->image);

            if($newImg){
                if(!ProductGallery::find()->where(['image' => $oldImg])->exists()){
                    File::delFile($oldImg, true);
                }
            }

            $this->image = $newImg;
        }

        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        if(!ProductGallery::find()->where(['image' => $this->image])->exists()){
            File::delFile($this->image, true);
        }
    }

    /**
     * 获取图片
     *
     * @param int $width
     * @param int $height
     *
     * @return mixed
     */
    public function getImage($width = 0, $height = 0)
    {
        return Image::getImg($this->image, $width, $height);
    }
}
