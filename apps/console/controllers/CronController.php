<?php

namespace console\controllers;

use admin\models\StoreStatement;
use api\models\Order;
use api\models\Store;
use api\models\User;
use api\models\UserIncome;
use Yii;
use yii\console\Controller;

class CronController extends Controller
{

    public function actionUser($begin = '')
    {
        if(!$begin){
            $begin = date('Y-m-d 00:00:00');
        }else{
            $begin = $begin . ' 00:00:00';
        }
        $begin = strtotime($begin);

        $end = strtotime(date('Y-m-d 00:00:00')) + 3600 * 24;

        $users = User::find()->where([
            'and',
            ['between', 'expire_at', $begin, $end],
            ['=', 'status', User::STATUS_ACTIVE],
            ['<>', 'open_id', ''],
        ])->all();
        foreach($users as $user){
            $user->syncUpdate();
        }
    }

    public function actionBalance()
    {
        $incomes = UserIncome::find()->where(['synced' => 0])->all();

        $userAmount = [];
        foreach($incomes as $income){
            if(isset($userAmount[$income->user_id])){
                $userAmount[$income->user_id]['amount'] += $income->amount;
            }else{
                $userAmount[$income->user_id]['amount'] = $income->amount;
            }

            $userAmount[$income->user_id]['income'][] = $income->id;
        }

        foreach($userAmount as $user_id => $item){
            $user = User::findOne($user_id);

            if($user){
                if($item['amount'] > $user->debt){
                    $balance = $item['amount'] - $user->debt;
                    if($user->syncBalance($balance)){
                        $user->amount += $balance;
                        if(!$user->save()){
                            UserIncome::updateAll(['synced' => 1], ['id' => $item['income']]);
                        }else{
                            UserIncome::updateAll(['synced' => 2], ['id' => $item['income']]);
                        }
                    }
                }else{
                    $user->debt -= $item['amount'];
                    if($user->save()){
                        UserIncome::updateAll(['synced' => 2], ['id' => $item['income']]);
                    }
                }
            }
        }
    }

    public function actionStatement()
    {
        $stores = Store::find()->where(['is_offline' => Store::IS_OFFLINE_YES])->all();

        foreach($stores as $store){
            $params['store_id'] = $store->store_id;
            $params['date'] = date("Y-m", strtotime('-1 month'));

            StoreStatement::deleteAll($params);

            $params['offline'] = Order::find()->where([
                'and',
                ['=', 'store_id', $store->store_id],
                ['=', 'status', Order::STATUS_DONE],
                ['=', 'is_offline', Order::IS_OFFLINE_YES],
                ['between', 'created_at', strtotime(date("Y-m-1", strtotime('-1 month'))), strtotime(date("Y-m-1"))],
            ])->sum('amount - cost - fee');

            $params['online'] = Order::find()->where([
                'and',
                ['=', 'store_id', $store->store_id],
                ['=', 'status', Order::STATUS_DONE],
                ['=', 'is_offline', Order::IS_OFFLINE_NO],
                ['between', 'created_at', strtotime(date("Y-m-1", strtotime('-1 month'))), strtotime(date("Y-m-1"))],
            ])->sum('amount - cost - fee');

            (new StoreStatement($params))->save();
        }
    }
}