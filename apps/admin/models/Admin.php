<?php

namespace admin\models;

use admin\traits\ModelStatus;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * {@inheritdoc}
 *
 * @property string $password write-only password
 * @property string $repassword write-only password
 *
 * @property AdminRole $role
 */
class Admin extends \common\models\Admin implements IdentityInterface
{

    use ModelStatus;

    const STATUS_DELETE = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 9;

    public $password;
    public $repassword;

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
            ['username', 'unique', 'message' => '该登录账号已存在'],
            ['password', 'safe', 'on' => 'update'],
            ['password', 'required', 'on' => ['create', 'reset']],
            ['repassword', 'required', 'on' => 'reset'],
            ['repassword', 'compare', 'compareAttribute' => 'password', 'message' => '两次密码输入不一致', 'on' => 'reset'],
            [['role_id'], 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETE]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'role_id' => '角色',
            'password' => '登录密码',
            'repassword' => '确认密码',
        ]);
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
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
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
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AdminRole::className(), ['id' => 'role_id']);
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        return $this->role ? $this->role->name : '无角色';
    }

    /**
     * @return array
     */
    public function getRoleSelectData()
    {
        return AdminRole::find()->select('name')->indexBy('id')->column();
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
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
}
