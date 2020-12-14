<?php

namespace api\models;

use Yii;
use yii\base\Model;

/**
 * Reset form
 *
 * @property $mobile
 * @property $vcode
 * @property $password
 */
class ResetForm extends Model
{
    public $mobile;
    public $vcode;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required'],
            ['mobile', 'match', 'pattern' => '/^1[3|4|5|7|8][0-9]{9}$/'],

            ['vcode', 'trim'],
            ['vcode', 'required'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @return bool|null
     */
    public function reset()
    {
        if(!$this->validate()){
            return NULL;
        }

        $vcode = Yii::$app->cache->get('api_vcode_' . $this->mobile);
        if($vcode != $this->vcode){
            $this->addError('referee', '验证码无效！');
            return NULL;
        }

        $user = User::findByMobile($this->mobile);

        if(!$user){
            $this->addError('mobile', '用户不存在！');

            return false;
        }

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateAccessToken();

        return $user->save();
    }
}
