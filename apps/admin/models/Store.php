<?php

namespace admin\models;

use ijony\helpers\File;
use ijony\helpers\Image;
use ijony\helpers\Utils;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%store}}".
 *
 * {@inheritdoc}
 */
class Store extends \common\models\Store
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $datas = Image::recoverImg($this->content);

        $this->content = $datas['content'];

        if($this->preview && substr($this->preview, 0, 6) == BUFFER_FOLDER){
            $oldImg = $this->preview;
            $newImg = Image::copyImg($this->preview);

            if($newImg){
                File::delFile($oldImg, true);
            }

            $this->preview = $newImg;
        }

        if($this->owner > 0){
            /* @var User $owner */
            $owner = User::find()->where(['mobile' => $this->owner])->one();

            if(!$owner){
                $this->addError('owner', '店主账号不存在！');

                return false;
            }

            $this->owner = $owner->user_id;
        }else{
            $this->owner = 0;
        }

        if($this->referee > 0){
            /* @var User $referee */
            $referee = User::find()->where(['mobile' => $this->referee])->one();

            if(!$referee){
                $this->addError('referee', '推广人不存在！');

                return false;
            }

            $this->referee = $referee->user_id;
        }else{
            $this->referee = 0;
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['preview']) && $changedAttributes['preview']){
            File::delFile($changedAttributes['preview'], true);
        }

        User::updateAll(['store' => 0], [
            'and',
            ['=', 'store', $this->store_id],
            ['<>', 'user_id', $this->owner],
        ]);
        User::updateAll(['store' => $this->store_id], ['user_id' => $this->owner]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        File::delFile($this->preview, true);
    }

    /**
     * @return string
     */
    public function getOwner($field)
    {
        $user = User::findOne($this->owner);

        return $user ? $user->$field : '';
    }

    /**
     * @return string
     */
    public function getReferee($field)
    {
        $user = User::findOne($this->referee);

        return $user ? $user->$field : '';
    }

    /**
     * 获取主图
     *
     * @return mixed
     */
    public function getPreview()
    {
        return Image::getImg($this->preview);
    }

    /**
     * 获取表单项
     *
     * @param null $where
     *
     * @return string
     */
    public static function getOptions($where = null)
    {
        $query = Store::find()->select('name');

        if($where){
            $query->where($where);
        }

        $datas = $query->indexBy('store_id')->column();
        $params = ['prompt' => '请选择'];

        return Html::renderSelectOptions(null, $datas, $params);
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
        if($this->status == self::STATUS_ACTIVE){
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
            self::STATUS_UNACTIVE => '禁用',
            self::STATUS_ACTIVE => '启用',
        ];
    }
}
