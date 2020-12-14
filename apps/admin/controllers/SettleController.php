<?php

namespace admin\controllers;

use admin\models\UserSettle;
use common\models\UserIncome;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * 奖励结算类
 *
 * @auth_key    settle
 * @auth_name   奖励结算
 */
class SettleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('settle'),
            ],
        ];
    }

    /**
     * 奖励结算列表
     *
     * @auth_key    *
     * @auth_parent settle
     *
     * @param string $date
     *
     * @return string
     */
    public function actionIndex($date = '')
    {
        if(!$date){
            $date = date("Y-m", strtotime('last month'));
        }

        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);

        $data = UserSettle::find()->groupBy(['year', 'month'])->all();

        $dates = [];
        foreach($data as $datum){
            $dates[$datum->year . '-' . $datum->month] = $datum->year . '-' . $datum->month;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => UserSettle::find()->where(['year' => $year, 'month' => $month]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'date' => $date,
            'dates' => $dates,
        ]);
    }

    /**
     * 奖励结算
     *
     * @auth_key    *
     * @auth_parent settle
     *
     * @param $user_id
     * @param $year
     * @param $month
     *
     * @return string
     */
    public function actionDone($user_id, $year, $month)
    {
        $model = UserSettle::find()->where(['year' => $year, 'month' => $month, 'user_id' => $user_id])->one();

        if(!$model){
            return $this->message('参数错误！');
        }

        $model->status = 1;
        $model->save();

        return $this->message('结算完成！');
    }
}
