<?php

namespace console\controllers;

use api\models\User;
use common\models\UserIncome;
use common\models\UserSettle;
use Yii;
use yii\console\Controller;

class SettleController extends Controller
{

    /**
     * @param string $date
     */
    public function actionGen($date = '')
    {
        if(!$date){
            $date = date("Y-m", strtotime('last month'));
        }

        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);

        /* @var \common\models\UserIncome[] $incomes */
        $incomes = UserIncome::find()->where(['date' => $date])->all();

        foreach($incomes as $income){
            $settle = UserSettle::find()->where(['year'    => $year,
                                                 'month'   => $month,
                                                 'user_id' => $income->user_id
            ])->one();
            if(!$settle){
                $settle = new UserSettle(['year' => $year, 'month' => $month, 'user_id' => $income->user_id]);
                $settle->amount = 0;
                $settle->status = 0;
            }

            $settle->amount = $settle->amount + $income->amount;
            $settle->save();
        }
    }

    public function actionCover()
    {
        User::updateAll(['amount' => 0], '1=1');

        $settles = \admin\models\UserSettle::find()->where(['status' => 0])->all();
        foreach($settles as $settle){
            $user = User::findOne($settle->user_id);

            if(!$user){
                continue;
            }

            $user->amount = $user->amount + $settle->amount;
            $user->save();
        }
    }
}