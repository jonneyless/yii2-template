<?php

namespace admin\models;

use libs\Utils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%coupon}}".
 *
 * {@inheritdoc}
 */
class Coupon extends \common\models\Coupon
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
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

    /**
     * 生成唯一码
     *
     * @return string
     */
    public static function genCode()
    {
        $code = Utils::getRand(15);

        if(self::find()->where(['code' => $code])->exists()){
            $code = self::genCode();
        }

        return $code;
    }
}
