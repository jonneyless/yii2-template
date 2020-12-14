<?php

namespace admin\controllers;

use admin\models\OrderGoods;
use Yii;
use admin\models\Order;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 订单管理类
 *
 * @auth_key    order
 * @auth_name   订单管理
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('order'),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 订单管理
     *
     * @auth_key    order_index
     * @auth_name   订单管理
     * @auth_parent order
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \admin\models\search\Order();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 订单详情
     *
     * @auth_key    order_view
     * @auth_name   订单详情
     * @auth_parent order
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getItems(),
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 发货管理
     *
     * @auth_key    order_delivery
     * @auth_name   发货管理
     * @auth_parent order
     *
     * @return string
     */
    public function actionDelivery()
    {
        $searchModel = new \admin\models\search\Order([
            'status' => Order::STATUS_PAID,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('delivery', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_PAID])->orderBy(['created_at' => SORT_DESC]),
        ]);
    }

    /**
     * 发货操作
     *
     * @auth_key    order_delivery_done
     * @auth_name   发货操作
     * @auth_parent order
     *
     * @param $id
     *
     * @return string
     */
    public function actionDeliveryDone($id)
    {
        $model = Order::findOne($id);

        if($model->load(Yii::$app->request->post()) && $model->delivery()){
            return $this->redirect(['delivery']);
        }

        return $this->render('delivery-done', [
            'model' => $model,
        ]);
    }

    /**
     * 退款管理
     *
     * @auth_key    order_refund
     * @auth_name   退款管理
     * @auth_parent order
     *
     * @return string
     */
    public function actionRefund()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where(['status' => Order::STATUS_REFUND])->orderBy(['created_at' => SORT_DESC]),
        ]);

        return $this->render('refund', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Order::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
