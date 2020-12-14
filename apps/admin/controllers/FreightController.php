<?php

namespace admin\controllers;

use Yii;
use admin\models\StoreFreight;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 店铺运费管理类
 *
 * @auth_key    freight
 * @auth_name   店铺运费模板管理
 */
class FreightController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('freight'),
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
     * 运费模板列表
     *
     * @auth_key    *
     * @auth_parent freight
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = StoreFreight::find();

        if($this->store_id){
            $query->andFilterWhere(['store_id' => $this->store_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加运费模板
     *
     * @auth_key    freight_create
     * @auth_name   添加运费模板
     * @auth_parent freight
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StoreFreight();

        if($this->store_id){
            $model->store_id = $this->store_id;
        }

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑运费模板
     *
     * @auth_key    freight_update
     * @auth_name   编辑运费模板
     * @auth_parent freight
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!$model->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除运费模板
     *
     * @auth_key    freight_delete
     * @auth_name   删除运费模板
     * @auth_parent freight
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
     * 获取运费模板对象
     *
     * @param $id
     *
     * @return \admin\models\StoreFreight
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = StoreFreight::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
