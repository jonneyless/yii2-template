<?php

namespace api\models;

use ijony\helpers\Utils;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%user_renew}}".
 *
 * {@inheritdoc}
 * @property \api\models\User $user
 */
class UserRenew extends \common\models\UserRenew
{

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    public function isDone()
    {
        return $this->status === self::STATUS_DONE;
    }

    public function paid(Payment $payment)
    {
        if($this->isDone()){
            return;
        }

        if($this->user->expire_at < time()){
            $this->user->expire_at = strtotime("+1 day", strtotime(date("Y-m-d", time())));
        }

        $this->user->expire_at += ($this->month * 3600 * 24 * 30);

        if(!$this->user->save()){
            throw new Exception('会员到期时间更新失败！');
        }

        $this->payment_id = $payment->payment_id;
        $this->status = self::STATUS_DONE;

        if(!$this->save()){
            throw new Exception('续费单更新失败！');
        }

        $this->setReward();
        $this->user->syncUpdate();
    }

    public function setReward()
    {
        if(!$this->user->referee){
            return;
        }

        $referee = User::findOne($this->user->referee);

        if(!$referee){
            return;
        }

        $rewards = [];
        $rewardUsers = [];

        $model = new UserIncome();
        $model->user_id = $referee->user_id;
        $model->type = UserIncome::TYPE_DIRECT;
        $model->relation_id = $this->user_id;
        $model->relation_type = UserIncome::RELATION_TYPE_USER;
        $model->amount = $this->amount * 0.2;
        $model->description = '推荐会员充值奖励！';
        $model->extend = Json::encode([
            'signup_at' => $this->user->created_at,
            'renew_id' => $this->renew_id,
        ]);
        $model->date = date("Y-m", $this->created_at);
        $model->created_at = $this->created_at;
        $model->updated_at = $this->created_at;

        if(!$model->save()){
            throw new Exception('推荐奖励更新失败！');
        }

        if($referee->referee){
            $topReferee = User::findOne($referee->referee);

            if($topReferee){
                $model = new UserIncome();
                $model->user_id = $topReferee->user_id;
                $model->type = UserIncome::TYPE_INDIRECT;
                $model->relation_id = $this->user_id;
                $model->relation_type = UserIncome::RELATION_TYPE_USER;
                $model->amount = $this->amount * 0.1;
                $model->description = '间接推荐会员充值奖励！';
                $model->extend = Json::encode([
                    'signup_at' => $this->user->created_at,
                    'renew_id' => $this->renew_id,
                ]);
                $model->date = date("Y-m", $this->created_at);
                $model->created_at = $this->created_at;
                $model->updated_at = $this->created_at;

                if(!$model->save()){
                    throw new Exception('间接推荐奖励更新失败！');
                }
            }
        }

        if($this->user->company){
            $companyReferee = User::findOne($this->user->company);

            if($companyReferee){
                $model = new UserIncome();
                $model->user_id = $companyReferee->user_id;
                $model->type = UserIncome::TYPE_COMPANY;
                $model->relation_id = $this->user_id;
                $model->relation_type = UserIncome::RELATION_TYPE_USER;
                $model->amount = $this->amount * 0.2;
                $model->description = '公司会员充值奖励！';
                $model->extend = Json::encode([
                    'signup_at' => $this->user->created_at,
                    'renew_id' => $this->renew_id,
                ]);
                $model->date = date("Y-m", $this->created_at);
                $model->created_at = $this->created_at;
                $model->updated_at = $this->created_at;

                if(!$model->save()){
                    throw new Exception('公司推荐奖励更新失败！');
                }

                if($companyReferee->company){
                    $companyTopReferee = User::findOne($companyReferee->company);

                    if($companyTopReferee){
                        $model = new UserIncome();
                        $model->user_id = $companyTopReferee->user_id;
                        $model->type = UserIncome::TYPE_COMPANY;
                        $model->relation_id = $this->user_id;
                        $model->relation_type = UserIncome::RELATION_TYPE_USER;
                        $model->amount = $this->amount * 0.05;
                        $model->description = '子公司会员充值奖励！';
                        $model->extend = Json::encode([
                            'signup_at' => $this->user->created_at,
                            'renew_id' => $this->renew_id,
                        ]);
                        $model->date = date("Y-m", $this->created_at);
                        $model->created_at = $this->created_at;
                        $model->updated_at = $this->created_at;

                        if(!$model->save()){
                            throw new Exception('子公司推荐奖励更新失败！');
                        }

                        if(isset($rewards[$model->user_id])){
                            $rewards[$model->user_id] += $model->amount;
                        }else{
                            $rewards[$model->user_id] = $model->amount;
                        }
                        $rewardUsers[$model->user_id] = $companyTopReferee;
                    }
                }
            }
        }
    }

    /**
     * 生成唯一订单号
     *
     * @return string
     */
    public static function genId()
    {
        $id = date("YmdHis", time()) . Utils::getRand(6, true);

        if(self::find()->where(['renew_id' => $id])->exists()){
            $id = self::genId();
        }

        return $id;
    }
}
