<?php

namespace admin\controllers;

use Yii;
use admin\models\StoreCategory;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 店铺商品分类管理类
 *
 * @auth_key  store_category
 * @auth_name 店铺商品分类管理
 */
class StoreCategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('store-category'),
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
     * 分类列表
     *
     * @auth_key    *
     * @auth_parent store_category
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = StoreCategory::find();

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
     * 添加分类
     *
     * @auth_key    store_category_create
     * @auth_name   添加分类
     * @auth_parent store_category
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new StoreCategory();
        $model->status = StoreCategory::STATUS_ACTIVE;

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
     * 编辑分类
     *
     * @auth_key    store_category_update
     * @auth_name   添加分类
     * @auth_parent store_category
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
     * 删除分类
     *
     * @auth_key    store_category_delete
     * @auth_name   添加分类
     * @auth_parent store_category
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
     * 获取分类对象
     *
     * @param $id
     *
     * @return \admin\models\StoreCategory
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = StoreCategory::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
