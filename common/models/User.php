<?php
namespace common\models;

use libs\Utils;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $id
 * @property string $auth_key
 * @property string $name
 * @property string $mobile
 * @property string $created_at
 * @property string $updated_at
 * @property string $signup_at
 * @property string $first_pay
 * @property integer $is_vip
 * @property integer $sign_status
 * @property integer $login_status
 * @property integer $status
 */
class User extends namespace\base\User implements IdentityInterface
{

    public $file;

    public $vcode;

    /**
     * @var 不是 VIP
     */
    const IS_VIP_NO = 0;
    /**
     * @var 是 VIP
     */
    const IS_VIP_YES = 9;

    /**
     * @var 注销
     */
    const LOGIN_NO = 0;
    /**
     * @var 登录
     */
    const LOGIN_YES = 9;

    /**
     * @var 禁用
     */
    const STATUS_DELETED = 0;
    /**
     * @var 启用
     */
    const STATUS_UNACTIVE = 1;
    const STATUS_ACTIVE = 9;

    const SIGN_STATUS_NO = 0;
    const SIGN_STATUS_WAIT = 1;
    const SIGN_STATUS_YES = 9;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['is_vip', 'default', 'value' => self::IS_VIP_NO],
            ['is_vip', 'in', 'range' => [self::IS_VIP_NO, self::IS_VIP_YES]],
            ['login_status', 'default', 'value' => self::LOGIN_NO],
            ['login_status', 'in', 'range' => [self::LOGIN_NO, self::LOGIN_YES]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_UNACTIVE, self::STATUS_DELETED]],
            ['file', 'file', 'extensions' => 'xlsx'],
            ['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '该用户已存在，请重新输入'],
            ['vcode', 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * 使用手机号码，查询用户信息
     *
     * @param string $mobile
     *
     * @return static|null
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile, 'status' => [self::STATUS_ACTIVE, self::STATUS_UNACTIVE]]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 生成登录保持密钥
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['user_id' => 'id'])->andWhere(['is_default' => 1]);
    }

    public function showMobile()
    {
        return $this->id == Yii::$app->user->id ? $this->mobile : Utils::starcode($this->mobile);
    }

    public function checkSignStatus()
    {
        return $this->sign_status == self::SIGN_STATUS_YES;
    }

    public function getIsNeedCheck()
    {
        return $this->sign_status == self::SIGN_STATUS_WAIT;
    }

    public function showSignStatus()
    {
        $signStatus = self::getSignStatusSelectData();

        return isset($signStatus[$this->sign_status]) ? $signStatus[$this->sign_status] : '非签约';
    }

    public static function getSignStatusSelectData()
    {
        return [
            self::SIGN_STATUS_NO => '非签约',
            self::SIGN_STATUS_YES => '已签约',
        ];
    }
}
