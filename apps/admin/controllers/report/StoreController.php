<?php

namespace admin\controllers\report;

use admin\controllers\Controller;
use admin\models\Goods;
use admin\models\Order;
use admin\models\Store;
use admin\models\StoreStatement;
use admin\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * 店铺报表类
 *
 * @auth_key    store_report
 * @auth_name   店铺报表管理
 */
class StoreController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('report/store'),
            ],
        ];
    }

    /**
     * 店铺账号列表
     *
     * @auth_key    *
     * @auth_parent store_report
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Store::find()->where(['is_offline' => Store::IS_OFFLINE_YES]),
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
        $store = Store::findOne($id);

        if(!$store || $store->is_offline != Store::IS_OFFLINE_YES){
            return $this->message('店铺不存在！');
        }

        $model = User::find()->where(['store' => $id, 'type' => User::TYPE_COMPANY])->one();

        $data['direct'] = $model ? User::find()->where(['referee' => $model->user_id])->count() : 0;
        $data['indirect'] = $model ? User::find()->where([
            'and',
            ['<>', 'referee', $model->user_id],
            ['=', 'company', $model->user_id],
        ])->count() : 0;

        $result = Yii::$app->db->createCommand('select (sum(g.cost_price) * i.stock) as cost_price from goods as g left join goods_info as i on g.goods_id = i.goods_id where g.store_id = ' . $id . ' and g.status = ' . Goods::STATUS_ACTIVE)->queryOne();
        $data['stockCosting'] = $result['cost_price'];

        $result = Yii::$app->db->createCommand('select (sum(g.member_price) * i.stock) as member_price from goods as g left join goods_info as i on g.goods_id = i.goods_id where g.store_id = ' . $id . ' and g.status = ' . Goods::STATUS_ACTIVE)->queryOne();
        $data['stockPrice'] = $result['member_price'];

        $data['saleIncome'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
        ])->sum('amount');

        $data['saleCosting'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
        ])->sum('cost');

        $data['offlineTodayIncome'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
            ['=', 'is_offline', Order::IS_OFFLINE_YES],
            ['between', 'created_at', strtotime('today'), strtotime('tomorrow')],
        ])->sum('amount');

        $data['offlineMonthIncome'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
            ['=', 'is_offline', Order::IS_OFFLINE_YES],
            ['between', 'created_at', strtotime(date("Y-m-1")), strtotime('tomorrow')],
        ])->sum('amount');

        return $this->render('view', [
            'store' => $store,
            'data' => $data,
        ]);
    }

    /**
     * 店铺账号会员列表
     *
     * @auth_key    *
     * @auth_parent store_report
     *
     * @param $id
     *
     * @return string
     */
    public function actionMember($id)
    {
        $store = Store::findOne($id);

        if(!$store || $store->is_offline != Store::IS_OFFLINE_YES){
            return $this->message('店铺不存在！');
        }

        $model = User::find()->where(['store' => $id, 'type' => User::TYPE_COMPANY])->one();

        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where([
                'and',
                ['=', 'company', $model->user_id],
            ]),
        ]);

        return $this->render('member', [
            'dataProvider' => $dataProvider,
            'store' => $store,
        ]);
    }

    /**
     * 店铺账号月结对账单
     *
     * @auth_key    *
     * @auth_parent store_report
     *
     * @param $id
     *
     * @return string
     */
    public function actionStatement($id)
    {
        $store = Store::findOne($id);

        if(!$store || $store->is_offline != Store::IS_OFFLINE_YES){
            return $this->message('店铺不存在！');
        }

        $currentMonth['offline'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
            ['=', 'is_offline', Order::IS_OFFLINE_YES],
            ['between', 'updated_at', strtotime(date("Y-m-1")), strtotime('tomorrow')],
        ])->sum('amount - cost - fee');

        $currentMonth['online'] = Order::find()->where([
            'and',
            ['=', 'store_id', $id],
            ['=', 'status', Order::STATUS_DONE],
            ['=', 'is_offline', Order::IS_OFFLINE_NO],
            ['between', 'updated_at', strtotime(date("Y-m-1")), strtotime('tomorrow')],
        ])->sum('amount - cost - fee');

        $dataProvider = new ActiveDataProvider([
            'query' => StoreStatement::find()->where(['store_id' => $id])->orderBy(['date' => SORT_DESC]),
        ]);

        return $this->render('statement', [
            'dataProvider' => $dataProvider,
            'currentMonth' => $currentMonth,
            'store' => $store,
        ]);
    }
}
