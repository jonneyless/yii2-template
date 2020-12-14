<?php

namespace api\models;

use Yii;
use yii\base\Model;

/**
 * Trade Pass form
 *
 * @property $vcode
 * @property $password
 */
class TradePassForm extends Model
{
    public $vcode;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['vcode', 'trim'],
            ['vcode', 'required'],

            ['password', 'required'],
        ];
    }

    /**
     * @return bool|null
     */
    public function save()
    {
        if(!$this->validate()){
            return NULL;
        }

        $vcode = Yii::$app->cache->get('api_vcode_' . Yii::$app->user->identity->mobile);
        if($vcode != $this->vcode){
            $this->addError('vcode', '验证码无效！');
            return NULL;
        }

        if(strlen($this->password) != 6){
            $this->addError('password', '交易密码只能是6位数字！');
            return NULL;
        }

        if(!preg_match('/^\d{6}$/', $this->password)){
            $this->addError('password', '交易密码只能是6位数字！');
            return NULL;
        }

        Yii::$app->user->identity->setTradePassword($this->password);

        if(Yii::$app->user->identity->save()){
            Yii::$app->user->identity->tradepass = $this->password;
            Yii::$app->user->identity->syncTradepass();

            return true;
        }

        return false;
    }
}
