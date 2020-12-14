<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use libs\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%comment}}".
 *
 * {@inheritdoc}
 *
 * @property string $username;
 * @property \admin\models\User $user
 * @property \admin\models\Goods $goods
 * @property \admin\models\CommentImage[] $images
 */
class Comment extends \common\models\Comment
{

    public $username;
    public $imgs;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['username', 'imgs'], 'safe'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'username' => '评论人',
        ]);
    }

    public function beforeSave($insert)
    {
        if($insert){
            $user = null;

            if($this->username){
                $user = User::find()->where(['username' => $this->username, 'status' => User::STATUS_DISGUISE])->one();

                if(!$user){
                    $user = new User(['username' => $this->username, 'status' => User::STATUS_DISGUISE]);
                    $user->auth_key = Yii::$app->security->generateRandomString();
                    $user->access_token = Yii::$app->security->generateRandomString();
                    $user->password_hash = Yii::$app->security->generatePasswordHash('1234567890');
                    $user->mobile = Utils::getRand(10, true);
                    $user->save();
                }
            }

            if(!$user){
                $user = User::find()->where(['status' => User::STATUS_DISGUISE])->orderBy('rand()')->one();
            }

            if(!$user){
                $this->addError('username', '请设置评论人！');
                return false;
            }

            $this->user_id = $user->user_id;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $newImage = [];

        if(isset($this->imgs['image'])){
            $newImage = $this->imgs['image'];
        }

        if($this->images){
            foreach($this->images as $image){
                if(!in_array($image->image, $newImage)){
                    $image->delete();
                }else{
                    $newImage = array_diff($newImage, [$image->image]);
                }
            }
        }

        if($newImage){
            foreach($newImage as $image){
                if($image && substr($image, 0, 6) == BUFFER_FOLDER){
                    $newImg = Image::copyImg($image);

                    if($newImg){
                        File::delFile($image, true);
                    }

                    $image = $newImg;
                }

                $commentImage = new CommentImage();
                $commentImage->comment_id = $this->comment_id;
                $commentImage->image = $image;
                $commentImage->save();
            }
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    public function getImages()
    {
        return $this->hasMany(CommentImage::className(), ['comment_id' => 'comment_id']);
    }

    /**
     * 获取状态表述
     *
     * @return mixed|string
     */
    public function getStatus()
    {
        $datas = $this->getStatusSelectData();

        return isset($datas[$this->status]) ? $datas[$this->status] : '';
    }

    /**
     * 获取状态标签
     *
     * @return mixed|string
     */
    public function getStatusLabel()
    {
        if($this->status == self::STATUS_SHOW){
            $class = 'label-primary';
        }else{
            $class = 'label-danger';
        }

        return Utils::label($this->getStatus(), $class);
    }

    /**
     * 获取完整状态数据
     *
     * @return array
     */
    public function getStatusSelectData()
    {
        return [
            self::STATUS_HIDDEN => '隐藏',
            self::STATUS_SHOW => '显示',
        ];
    }
}
