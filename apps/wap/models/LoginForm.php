<?php

namespace wap\models;

use common\models\User;
use libs\ccbpay\Ccb;
use libs\SMS;
use libs\Utils;
use Yii;
use yii\base\Model;

/**
 * Login form
 *
 * @property string $mobile
 * @property string $vcode
 */
class LoginForm extends Model
{
    public $mobile;
    public $vcode;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'vcode'], 'required'],
            ['vcode', 'validateVcode'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'vcode' => '验证码',
        ];
    }

    public function validateVcode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $msg = SMS::validator($this->vcode);

            if ($msg) {
                $this->addError($attribute, $msg);
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 3600 * 2);
        } else {
            return false;
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $user = User::findByMobile($this->mobile);
            if (!$user) {
                $user = new User();
                $user->name = '用户' . $this->mobile;
                $user->mobile = $this->mobile;
                $user->generateAuthKey();
                $user->sign_status = (new Ccb())->getSignStatus($this->mobile);
                $user->save();
            }

            $this->_user = $user;
        }

        return $this->_user;
    }
}