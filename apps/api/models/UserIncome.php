<?php

namespace api\models;

use ijony\helpers\Image;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%user_income}}".
 *
 * @property string $user_id 用户 ID
 * @property string $type 类型
 * @property string $relation_id 关联 ID
 * @property string $relation_type 关联类型
 * @property string $amount 金额
 * @property string $description 说明
 * @property string $extend 扩展数据
 * @property string $date 年月
 * @property int $created_at 生成时间
 * @property int $updated_at 更新时间
 *
 * @property \api\models\User|\api\models\Store $from
 * @property User $user
 */
class UserIncome extends \common\models\UserIncome
{

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->user->amount = $this->user->amount + $this->amount;
            $this->user->save();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFrom()
    {
        if($this->relation_type == self::RELATION_TYPE_STORE){
            return $this->hasOne(Store::className(), ['store_id' => 'relation_id']);
        }else{
            return $this->hasOne(User::className(), ['user_id' => 'relation_id']);
        }
    }

    /**
     * @return array
     * @throws \Imagine\Image\InvalidArgumentException
     */
    public function buildListData()
    {
        if($this->relation_type == self::RELATION_TYPE_STORE){
            return [
                'username'    => $this->from->name,
                'avatar'      => Image::getImg($this->from->preview, 0, 0, 'default.gif'),
                'rewarded_at' => date("Y-m-d", $this->created_at),
                'reward'      => $this->amount,
            ];
        }else{
            return [
                'username'    => $this->from->username,
                'avatar'      => Image::getImg($this->from->avatar, 0, 0, 'default-avatar.gif'),
                'rewarded_at' => date("Y-m-d", $this->created_at),
                'reward'      => $this->amount,
            ];
        }
    }
}
