<?php
namespace admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 用户数据模型
 *
 * {@inheritdoc}
 *
 * @property string $renew_month
 * @property string $tradepass
 */
class User extends \common\models\User
{

    public $renew_month;
    public $tradepass;

    public function beforeSave($insert)
    {
        if($this->renew_month){
            $this->expire_at = $this->expire_at > time() ? $this->expire_at : strtotime("+1 day", strtotime(date("Y-m-d", time())));
            $this->expire_at += ($this->renew_month * 3600 * 24 * 30);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['type'])){
            if($this->type == self::TYPE_COMPANY){
                $childs = User::find()->where(['referee' => $this->user_id])->all();
                foreach($childs as $child){
                    $child->company = $this->user_id;
                    $child->save();
                }
            }else{
                $childs = User::find()->where(['referee' => $this->user_id])->all();
                foreach($childs as $child){
                    $child->company = $this->company;
                    $child->save();
                }
            }
        }

        if(isset($changedAttributes['company'])){
            $childs = User::find()->where(['referee' => $this->user_id])->all();
            foreach($childs as $child){
                $child->company = $this->company;
                $child->save();
            }
        }
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['renew_month', 'integer'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'renew_month' => '充值月份',
        ]);
    }

    public function showReferee()
    {
        if(!$this->referee){
            return '';
        }

        $referee = User::findOne($this->referee);

        if(!$referee){
            return  '';
        }

        if($referee->store){
            $store = Store::findOne($referee->store);

            if($store){
                return $store->name;
            }
        }

        return $referee->username;
    }

    public function showExpire()
    {
        if(!$this->expire_at){
            return '';
        }

        return date("Y-m-d", $this->expire_at);
    }

    public function syncCreate()
    {
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

        if($this->tradepass_hash){
            $params['customerInfo']['password'] = '123456';
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
}
