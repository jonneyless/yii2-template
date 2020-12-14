<?php
namespace common\models;

use Yii;

/**
 * 用户数据模型
 *
 * {@inheritdoc}
 *
 * @property \common\models\UserAddress $address
 * @property \common\models\UserAddress[] $addresses
 */
class User extends namespace\base\User
{

    const TYPE_NORMAL = 0;
    const TYPE_AGENT = 1;
    const TYPE_COMPANY = 2;
    const TYPE_CITY = 3;

    const STATUS_DELETE = 0;    // 删除
    const STATUS_UNACTIVE = 1;  // 禁用
    const STATUS_DISGUISE = 2;  // 假数据
    const STATUS_ACTIVE = 9;    // 启用

    public function getInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }

    /**
     * 默认收货地址
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(UserAddress::className(), ['user_id' => 'user_id'])->andWhere(['is_default' => UserAddress::IS_DEFAULT_YES]);
    }

    /**
     * 所有收货地址
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['user_id' => 'user_id']);
    }
}
