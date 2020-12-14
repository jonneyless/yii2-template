<?php

namespace console\controllers;

use api\models\User;
use libs\SMS;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;

class UserController extends Controller
{

    public function actionReAmount()
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $users = User::find()->where([
            'or',
            ['>', 'amount', 0],
            ['>', 'debt', 0],
        ])->all();

        foreach($users as $user){
            $result = $pospal->user->findByMobile($user->mobile);
            $balance = $result->getFirstData('balance');

            if($user->amount != $balance){
                $user->syncBalance($user->amount - $balance);
            }
        }
    }

    public function actionBalance($id)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;
        $user = User::findOne($id);
        $result = $pospal->user->findByMobile($user->mobile);
        $balance = $result->getFirstData('balance');

        if($user->amount != $balance){
            $user->syncBalance($user->amount - $balance);
        }
    }

    public function actionPassword($mobile, $password)
    {
        $user = \api\models\User::findByMobile($mobile);
        $user->setPassword($password);
        $user->save();
    }

    public function actionPospal($mobile)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $result = $pospal->user->findByMobile($mobile);

        print_r($result);
    }

    public function actionPospalByCode($code)
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $result = $pospal->user->findByCode($code);

        print_r($result);
    }

    public function actionSyncUpdate($id)
    {
        $user = User::findOne($id);
        if($user){
            $user->syncUpdate();
        }
    }

    public function actionAmount($id, $amount)
    {
        $user = User::findOne($id);
        if($user){
            $user->syncBalance($amount);
        }
    }

    public function actionAdd()
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $users = User::find()->where([
            'and',
            ['=', 'open_id', ''],
            ['=', 'status', 9],
        ])->all();

        foreach($users as $user){
            $user->syncUpdate();
        }
    }

    public function actionSync()
    {
        /* @var \libs\pospal\Pospal $pospal */
        $pospal = Yii::$app->pospal;

        $users = User::find()->where([
            'and',
            ['<>', 'open_id', ''],
            ['=', 'status', 9],
            ['>', 'expire_at', time()],
        ])->all();

        foreach($users as $user){
            $user->syncUpdate();
        }
    }

    public function actionSms($mobile)
    {
        $vcode = rand(100000, 999999);
        $result = Yii::$app->sms->sendSms($mobile, 'SMS_151770413', ['code' => $vcode]);
        Yii::$app->cache->set('api_vcode_' . $mobile, $vcode, 1800);
        print_r($result);
        print_r($vcode);
    }
}