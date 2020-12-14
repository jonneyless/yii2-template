<?php
namespace api\models;

use Yii;
use yii\base\Model;

/**
 * Signin form
 *
 * @property $mobile
 * @property $password
 * @property $device
 */
class SigninForm extends Model
{
    public $mobile;
    public $password;
    public $device;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required', 'message' => '请输入账号'],
            ['mobile', 'match', 'pattern' => '/^1[3-9][0-9]{9}$/'],

            ['password', 'required', 'message' => '请输入密码'],
            ['password', 'validatePassword'],

            ['device', 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '账号',
            'password' => '密码',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if(!$user){
                $this->addError('mobile', '用户不存在');
            }else if(!$user->validatePassword($this->password)) {
                $this->addError('password', '密码错误');
            }
        }
    }

    /**
     * 登陆验证
     *
     * @return \api\models\User|bool|null
     */
    public function signin()
    {
        if ($this->validate()) {
            return $this->getUser();
        } else {
            return false;
        }
    }

    /**
     * 通过手机获取用户
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobile($this->mobile);
            if($this->_user){
                if($this->mobile != 13510470000){
                    $this->_user->generateAccessToken();
                }
                $userAgent = Yii::$app->request->getUserAgent();
                $deviceType = strpos($userAgent, 'iPhone') > 0 ? 'iphone' : 'android';
                $this->_user->device = $this->device ? $this->device : '';
                $this->_user->device_type = $deviceType;
                $this->_user->save();
            }
        }

        return $this->_user;
    }
}
