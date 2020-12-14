<?php

namespace admin\controllers;

use Yii;
use admin\models\Coupon;
use admin\models\form\Coupon as CouponForm;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 优惠券管理类
 *
 * @auth_key    coupon
 * @auth_name   优惠券模板管理
 */
class CouponController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('coupon'),
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
     * 优惠券列表
     *
     * @auth_key    *
     * @auth_parent coupon
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \admin\models\search\Coupon();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加优惠券
     *
     * @auth_key    coupon_create
     * @auth_name   添加优惠券
     * @auth_parent coupon
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CouponForm();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $model->export();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 删除优惠券
     *
     * @auth_key    coupon_delete
     * @auth_name   删除优惠券
     * @auth_parent coupon
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * 获取优惠券对象
     *
     * @param $id
     *
     * @return \admin\models\Coupon
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Coupon::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
