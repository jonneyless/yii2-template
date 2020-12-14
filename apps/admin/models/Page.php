<?php

namespace admin\models;

use ijony\helpers\Image;
use Yii;

/**
 * 页面数据模型
 *
 * {@inheritdoc}
 */
class Page extends \common\models\Page
{

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $datas = Image::recoverImg($this->content);

        $this->content = $datas['content'];

        return parent::beforeSave($insert);
    }
}
