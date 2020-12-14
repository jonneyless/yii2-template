<?php
namespace api\models;

use ijony\helpers\Image;
use ijony\helpers\Url;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * 用户数据模型
 *
 * {@inheritdoc}
 *
 * @property string $password write-only password
 * @property string $tradepass
 * @property string $balanceIncrement
 *
 * @property \api\models\UserAddress $address
 * @property \api\models\UserInfo $info
 * @property \api\models\UserAddress[] $addresses
 * @property \api\models\Order[] $order
 * @property \api\models\Service[] $service
 */
class User extends \common\models\User implements IdentityInterface
{

    public $password;
    public $repassword;
    public $tradepass;
    public $balanceIncrement;

    private static $_address;

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
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_DELETE, self::STATUS_UNACTIVE, self::STATUS_ACTIVE]],
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert){
            $info = new UserInfo();
            $info->user_id = $this->user_id;
            $info->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 通过手机号码获取用户
     *
     * @param string $mobile
     * @return static|null
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByOpenId($openId)
    {
        return static::findOne(['open_id' => $openId, 'status' => self::STATUS_ACTIVE]);
    }

    public function getInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }

    /**
     * 订单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasMany(Order::className(), ['user_id' => 'user_id']);
    }

    /**
     * 订单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasMany(Service::className(), ['user_id' => 'user_id']);
    }

    /**
     * 会员
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasMany(User::className(), ['referee' => 'user_id']);
    }

    /**
     * 收益
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIncome()
    {
        return $this->hasMany(UserIncome::className(), ['user_id' => 'user_id']);
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validateTradePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->tradepass_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setTradePassword($password)
    {
        $this->tradepass_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 生成接口登陆 Token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @param $token
     * @param $type
     *
     * @return null|\yii\web\IdentityInterface|static
     */
    public function loginByAccessToken($token, $type) {
        return self::findIdentityByAccessToken($token, $type);
    }

    /**
     * 获取接口 Access Token
     *
     * @return array
     */
    public function getApiAccessToken()
    {
        return ['access_token' => $this->access_token];
    }

    public function getAddressById($address_id)
    {
        if(self::$_address === null){
            if($address_id){
                self::$_address = UserAddress::find()->where(['user_id' => Yii::$app->user->id, 'address_id' => $address_id])->one();
            }else{
                self::$_address = $this->address;
            }
        }

        return self::$_address;
    }

    public function getDeliveryProvince($address_id)
    {
        $address = $this->getAddressById($address_id);

        if(!$address){
            return 0;
        }

        return $address->getTopAreaId();
    }

    public function getDeliveryAddress($address_id)
    {
        $address = $this->getAddressById($address_id);

        if(!$address){
            return null;
        }

        return [
            'address_id' => $address->address_id,
            'consignee' => $address->consignee,
            'phone' => $address->phone,
            'longitude' => $address->longitude,
            'latitude' => $address->latitude,
            'address' => $address->full_address,
        ];
    }

    public function checkExpire()
    {
        return $this->expire_at > time();
    }

    public function checkRole()
    {
        if($this->expire_at == 0){
            return 'visitor';
        }else if($this->checkExpire()){
            return 'vip';
        }else{
            return 'expired';
        }
    }

    public function isTeacher()
    {
        return Teacher::find()->where(['user_id' => $this->user_id])->exists();
    }

    public function buildViewData()
    {
        $params = [
            'username' => $this->username,
            'avatar' => Image::getImg($this->avatar, 0, 0, 'default-avatar.gif'),
            'mobile' => $this->mobile,
            'amount' => $this->amount,
            'expire' => date("Y-m-d", $this->expire_at),
            'birthday' => date("Y-m-d", $this->info->birthday),
            'gander' => $this->info->gander,
            'saving' => sprintf('%.02f', $this->getOrder()->where(['status' => Order::STATUS_DONE])->sum('saving')),
            'is_vip' => $this->checkExpire(),
            'is_teacher' => $this->isTeacher(),
            'role' => $this->checkRole(),
            'used_coupon' => false,
            'share' => Url::getFull('system/share-' . $this->user_id . '.html', 'wap'),
            'debug' => $this->mobile == '13510470000',
        ];

        return $params;
    }

    public function buildRewardData($user_id)
    {
        $reward = UserIncome::find()->where(['relation_id' => $this->user_id, 'user_id' => $user_id])->sum('amount');

        return [
            'username' => $this->username,
            'avatar' => Image::getImg($this->avatar, 0, 0, 'default-avatar.gif'),
            'created_at' => date("Y-m-d", $this->created_at),
            'reward' => $reward ? sprintf('%.02f', $reward) : '0.00',
            'referee' => $this->referee == $user_id,
        ];
    }

    public function updateBalance($amount)
    {
        if($amount > 0 && $this->amount < $amount){
            throw new ErrorException('账户余额不足！');
        }

        $this->amount = $this->amount - $amount;
        $this->save();
    }

    public function  syncBalance($amount)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        if(!$this->open_id){
            return;
        }

        $params = [
            'customerUid' => $this->open_id,
            'balanceIncrement' => $amount,
            'pointIncrement' => 0,
            'dataChangeTime' => date("Y-m-d H:i:s"),
        ];

        $result = $pospal->user->updateBalance($params);
        if($result->isSuccess()){
            return true;
        }

        return false;
    }

    public function syncCreate()
    {
        return;
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $params = [
            'customerInfo' => [
                "categoryName" => $this->expire_at > time() ? Yii::$app->params['member']['vip'] : Yii::$app->params['member']['normal'],
                "number" => "BYD" . $this->user_id,
                "name" => $this->username,
                "point" => 0,
                "discount" => 0,
                "balance" => $this->amount,
                "phone" => $this->mobile,
                "birthday" => '',
                "qq" => '',
                "email" => '',
                "address" => '',
                "remarks" => date("Y-m-d H:i:s", $this->expire_at),
                "onAccount" => 0,
                "enable" => 1,
            ],
        ];

        if($this->tradepass){
            $params['customerInfo']['password'] = $this->tradepass;
        }

        $result = $pospal->user->create($params);
        if($result->isSuccess()){
            $this->open_id = (string) $result->getData('customerUid');
            if(!$this->save()){
                Yii::error($result);
                Yii::error($this->getErrors());
            }
        }else if($result->isCode(2012)){
            $result = $pospal->user->findByMobile($this->mobile);
            if($result->isSuccess()) {
                $this->open_id = (string) $result->getFirstData('customerUid');
                if(!$this->save()){
                    Yii::error($result);
                    Yii::error($this->getErrors());
                }
            }
        }
    }

    public function syncUpdate()
    {
        return;
        if(!$this->open_id){
            $this->syncCreate();
        }else{
            /* @var \libs\pospal\Pospal $pospal */
            $pospal = Yii::$app->pospal;

            $params = [
                'customerInfo' => [
                    'customerUid' => $this->open_id,
                    "name" => $this->username,
                    "phone" => $this->mobile,
                    "birthday" => '',
                    "qq" => '',
                    "email" => '',
                    "address" => '',
                    "categoryName" => $this->expire_at > time() ? Yii::$app->params['member']['vip'] : Yii::$app->params['member']['normal'],
                    "remarks" => date("Y-m-d H:i:s", $this->expire_at),
                ]
            ];

            $result = $pospal->user->update($params);
        }
    }

    public function syncTradepass()
    {
        return;
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        if($this->open_id){
            if($this->tradepass){
                $params = [
                    'customerUid' => $this->open_id,
                    'customerPassword' => $this->tradepass,
                ];

                $result = $pospal->user->resetPass($params);
            }
        }else{
            $this->syncCreate();
        }
    }
}
