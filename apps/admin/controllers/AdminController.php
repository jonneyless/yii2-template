<?php

namespace admin\controllers;

use Yii;
use admin\models\Admin;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 管理员管理类
 *
 * @auth_key    admin
 * @auth_name   管理员管理
 */
class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('admin'),
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
     * 管理员管理列表
     *
     * @auth_key    *
     * @auth_parent admin
     *
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->user->id != 1){
            return $this->redirect(['view', 'id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Admin::find()->where(['<>', 'status', Admin::STATUS_DELETE]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 查看管理员详情
     *
     * @auth_key    admin_view
     * @auth_name   查看管理员
     * @auth_parent admin
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增活动管理员
     *
     * @auth_key    admin_create
     * @auth_name   添加管理员
     * @auth_parent admin
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Admin();
        $model->setScenario('create');

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 编辑活动管理员
     *
     * @auth_key    admin_update
     * @auth_name   更新管理员
     * @auth_parent admin
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 将管理员移动到回收站
     *
     * @auth_key    admin_delete
     * @auth_name   删除管理员
     * @auth_parent admin
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_DELETE;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * 管理员回收站管理
     *
     * @auth_key    admin_recycle
     * @auth_name   管理员回收站
     * @auth_parent admin
     *
     * @return string
     */
    public function actionRecycle()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Admin::find()->where(['=', 'status', Admin::STATUS_DELETE]),
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 将管理员从回收站恢复
     *
     * @auth_key    admin_recycle
     * @auth_name   管理员回收站
     * @auth_parent admin
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);
        $model->status = $model::STATUS_ACTIVE;
        $model->save();

        return $this->redirect(['recycle']);
    }

    /**
     * 彻底删除管理员
     *
     * @auth_key    admin_recycle
     * @auth_name   管理员回收站
     * @auth_parent admin
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
        $model->delete();

        return $this->redirect(['recycle']);
    }

    /**
     * 修改密码
     *
     * @auth_key    *
     * @auth_parent admin
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $model->setScenario('reset');

        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->user->logout();

            return $this->goHome();
        }else{
            return $this->render('reset', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\Admin
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Admin::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
