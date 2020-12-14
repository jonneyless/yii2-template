<?php

namespace admin\controllers;

use Yii;
use admin\models\Category;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 商品分类管理类
 *
 * @auth_key    category
 * @auth_name   商品分类管理
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('category'),
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
     * @auth_parent category
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \admin\models\search\Category();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加分类
     *
     * @auth_key    category_create
     * @auth_name   添加分类
     * @auth_parent category
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Category();
        $model->status = Category::STATUS_ACTIVE;

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
     * @auth_key    category_update
     * @auth_name   编辑分类
     * @auth_parent category
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

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
     * @auth_key    category_delete
     * @auth_name   删除分类
     * @auth_parent category
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 获取分类对象
     *
     * @param $id
     *
     * @return \admin\models\Category
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Category::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
