<?php

namespace admin\controllers\report;

use admin\controllers\Controller;
use admin\models\Order;
use admin\models\Store;
use admin\models\User;
use admin\models\UserIncome;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * 推荐人报表类
 *
 * @auth_key    referee_report
 * @auth_name   推荐人报表管理
 */
class RefereeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('report/referee'),
            ],
        ];
    }

    /**
     * 店铺账号列表
     *
     * @auth_key    *
     * @auth_parent store_report
     *
     * @param string $mobile
     *
     * @return string
     */
    public function actionIndex($mobile = '')
    {
        $userId = 0;

        if($mobile){
            $userId = User::find()->where(['mobile' => $mobile])->one()->user_id;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where([
                'and',
                ['=', 'type', User::TYPE_COMPANY],
                ['<>', 'store', 0],
                ['=', 'referee', $userId ? $userId : -1],
            ]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * 店铺账号报表详情
     *
     * @auth_key    *
     * @auth_parent store_report
     *
     * @param $id
     *
     * @return string
     */
    public function actionView($id)
    {
        $model = User::findOne($id);
        $referee = User::findOne($model->referee);

        $data['member'] = User::find()->where(['company' => $id])->count();
        $data['performance'] = Order::find()->where(['store_id' => $model->store])->sum('amount');
        $data['brokerage'] = UserIncome::find()->where(['user_id' => $referee->user_id, 'relation_id' => $model->store, 'relation_type' => 'Store'])->sum('amount');

        $data['offline_performance'] = Order::find()->where(['store_id' => $model->store, 'is_offline' => Order::IS_OFFLINE_YES])->sum('amount');
        $data['offline_costing'] = Order::find()->where(['store_id' => $model->store, 'is_offline' => Order::IS_OFFLINE_YES])->sum('cost');

        $data['online_performance'] = Order::find()->where(['store_id' => $model->store, 'is_offline' => Order::IS_OFFLINE_NO])->sum('amount');
        $data['online_costing'] = Order::find()->where(['store_id' => $model->store, 'is_offline' => Order::IS_OFFLINE_NO])->sum('cost');

        return $this->render('view', [
            'store' => Store::findOne($model->store),
            'data' => $data,
            'referee' => $referee,
        ]);
    }
}
