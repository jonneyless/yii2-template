<?php

namespace api\models;

use ijony\helpers\Url;
use libs\Utils;
use yii\base\Model;
use Yii;

/**
 * Signup form
 *
 * @property $mobile
 * @property $vcode
 * @property $referee
 * @property $area_id
 * @property $password
 * @property $device
 */
class SignupForm extends Model
{
    public $mobile;
    public $vcode;
    public $referee;
    public $area_id;
    public $password;
    public $device;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required', 'message' => '请输入账号'],
            ['mobile', 'unique', 'targetClass' => '\api\models\User', 'message' => '手机号码已注册。'],
            ['mobile', 'match', 'pattern' => '/^1[3-9][0-9]{9}$/'],

            ['vcode', 'trim'],
            ['vcode', 'required', 'message' => '请输入验证码'],

            ['referee', 'trim'],
            ['referee', 'required', 'message' => '请输入推荐人手机号码'],

            ['area_id', 'required', 'message' => '请选择地区'],

            ['password', 'required', 'message' => '请输入密码'],
            ['password', 'string', 'min' => 6],

            ['device', 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '账号',
            'vcode' => '验证码',
            'referee' => '推荐人',
            'area_id' => '地区',
            'password' => '密码',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if(!$this->validate()){
            return NULL;
        }

        $vcode = Yii::$app->cache->get('api_vcode_' . $this->mobile);
        if($vcode != $this->vcode){
            Yii::error($this->mobile . "|" . $vcode . "|" . $this->vcode, 'sms');
            $this->addError('referee', '验证码无效！');
            return NULL;
        }

        Yii::$app->cache->delete('api_vcode_' . $this->mobile);

        $referee = User::findByMobile($this->referee);
        if(!$referee){
            $this->addError('referee', '推荐人手机号不存在！');
            return NULL;
        }

        $user = new User();
        $user->username = '会员' . $this->mobile;
        $user->avatar = Url::getStatic('default-avatar.gif');
        $user->mobile = $this->mobile;
        $user->referee = $referee->user_id;
        if($referee->type == \common\models\User::TYPE_COMPANY){
            $user->company = $referee->user_id;
        }else{
            $user->company = $referee->company;
        }
        $user->area_id = $this->area_id;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateAccessToken();
        $user->device = $this->device ? $this->device : '';
        $user->expire_at = time() + 3600 * 24 * 365;

        if(!$user->save()){
            $this->addError('mobile', Utils::paserErrors($user->getFirstErrors()));
            return null;
        }

        $user->syncCreate();

        return $user;
    }
}
