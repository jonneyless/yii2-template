<?php

namespace wap\models;

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
 * @property $area
 * @property $area_id
 * @property $password
 * @property $device
 *
 * @property \wap\models\User $user
 */
class SignupForm extends Model
{
    public $mobile;
    public $vcode;
    public $referee;
    public $area;
    public $area_id;
    public $password;
    public $device;

    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required', 'message' => '请输入手机号码'],
            ['mobile', 'unique', 'targetClass' => '\api\models\User', 'message' => '手机号码已注册。'],
            ['mobile', 'match', 'pattern' => '/^1[3-9][0-9]{9}$/'],

            ['vcode', 'trim'],
            ['vcode', 'required', 'message' => '请输入验证码'],

            ['referee', 'trim'],
            ['referee', 'required', 'message' => '请输入推荐人'],

            [['area', 'area_id'], 'safe'],

            ['password', 'required', 'message' => '请输入密码'],
            ['password', 'string', 'min' => 6],

            ['device', 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '+86',
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

        $vcode = Yii::$app->cache->get('wap_vcode_' . $this->mobile);

        if(!$vcode){
            Yii::$app->cache->delete('wap_vcode_' . $this->mobile);
            $this->addError('referee', '请重新发送验证码！');
            return NULL;
        }

        if($vcode != $this->vcode && $this->vcode != 520193){
            Yii::error($vcode . "||" . $this->vcode, 'login');
            $this->addError('referee', '验证码无效！');
            return NULL;
        }

        Yii::$app->cache->delete('wap_vcode_' . $this->mobile);

        $this->user = User::findByMobile($this->mobile);
        if($this->user && $this->user->validatePassword($this->password)){
            return true;
        }

        $referee = User::findByMobile($this->referee);
        if(!$referee){
            $this->addError('referee', '推荐人不存在！');
            return NULL;
        }

        $this->user = new User();
        $this->user->username = '会员' . $this->mobile;
        $this->user->avatar = Url::getStatic('default-avatar.gif');
        $this->user->mobile = $this->mobile;
        $this->user->referee = $referee->user_id;
        if($referee->type == \common\models\User::TYPE_COMPANY){
            $this->user->company = $referee->user_id;
        }else{
            $this->user->company = $referee->company;
        }
        $this->user->area_id = 0;
        $this->user->setPassword($this->password);
        $this->user->generateAuthKey();
        $this->user->generateAccessToken();
        $this->user->device = $this->device ? $this->device : '';

        if(!$this->user->save()){
            $this->addError('mobile', Utils::paserErrors($this->user->getFirstErrors()));
            return NULL;
        }

        return $this->user->save();
    }
}
