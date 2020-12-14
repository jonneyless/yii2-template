<?php

namespace admin\controllers;

use Yii;
use admin\models\Page;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 静态单页管理类
 *
 * @auth_key    page
 * @auth_name   静态单页管理
 */
class PageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('page'),
            ],
        ];
    }

    /**
     * 单页列表
     *
     * @auth_key    *
     * @auth_parent page
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Page::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加单页
     *
     * @auth_key    page_create
     * @auth_name   添加单页
     * @auth_parent page
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Page();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑单页
     *
     * @auth_key    page_update
     * @auth_name   编辑单页
     * @auth_parent page
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
     * 获取单页对象
     *
     * @param $id
     *
     * @return \admin\models\Page
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Page::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
