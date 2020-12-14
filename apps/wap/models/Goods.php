<?php

namespace wap\models;

use ijony\helpers\Image;
use ijony\helpers\Url;
use ijony\helpers\Utils;
use Yii;

/**
 * 商品数据模型
 *
 * {@inheritdoc}
 */
class Goods extends \common\models\Goods
{

    public function getViewUrl()
    {
        return Url::to(['goods/view', 'id' => $this->goods_id]);
    }

    public function getPrice()
    {
        if(!Yii::$app->user->getIsGuest() && Yii::$app->user->identity->checkExpire()){
            return $this->member_price;
        }

        return $this->original_price;
    }

    public function showName($len = 50)
    {
        return Utils::substr($this->name, 0, $len);
    }

    public function showPreview($width, $height)
    {
        return Image::getImg($this->preview, $width, $height);
    }

    public function showContent()
    {
        $content = preg_replace('/ style="[^"]+"/', '', $this->content);
        $content = preg_replace('/ width="[^"]+"/', '', $content);
        $content = preg_replace('/ height="[^"]+"/', '', $content);

        return $content;
    }
}
