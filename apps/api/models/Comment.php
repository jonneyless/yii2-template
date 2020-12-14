<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;

/**
 * This is the model class for table "{{%comment}}".
 *
 * {@inheritdoc}
 *
 * @property \api\models\User $user
 * @property \api\models\OrderGoods $goods
 * @property \api\models\CommentImage $image
 */
class Comment extends \common\models\Comment
{

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(OrderGoods::className(), ['order_id' => 'order_id', 'goods_id' => 'goods_id']);
    }

    public function getImage()
    {
        return $this->hasMany(CommentImage::className(), ['comment_id' => 'comment_id']);
    }

    public function buildCommentListData()
    {
        $images = [];

        if($this->image){
            $images = array_map(function($item){
                /* @var $item \api\models\CommentImage */
                return Image::getImg($item->image, 0, 0, 'default.jpg');
            }, $this->image);
        }

        return [
            'comment_id' => $this->comment_id,
            'goods_score' => $this->goods_score,
            'goods_attrs' => $this->goods ? $this->goods->parseAttr() : [],
            'content' => $this->content,
            'user' => [
                'username' => $this->user->username,
                'avatar' => Image::getImg($this->user->avatar, 0, 0, 'default-avatar.jpg'),
            ],
            'images' => $images,
            'created_at' => date("Y-m-d", $this->created_at),
        ];
    }

    public function buildViewData()
    {
        $images = [];

        if($this->image){
            $images = array_map(function($item){
                /* @var $item \api\models\CommentImage */
                return Image::getImg($item->image, 0, 0, 'default.jpg');
            }, $this->image);
        }

        return [
            'comment_id' => $this->comment_id,
            'goods_score' => $this->goods_score,
            'store_score' => $this->store_score,
            'delivery_score' => $this->delivery_score,
            'content' => $this->content,
            'goods' => [
                'goods_id' => (int)$this->goods_id,
                'goods_name' => $this->goods->name,
                'preview' => Image::getImg($this->goods->preview, 300, 300, 'default.jpg'),
                'attrs' => $this->goods->parseAttr(),
            ],
            'images' => $images,
            'created_at' => date("Y-m-d", $this->created_at),
        ];
    }
}
