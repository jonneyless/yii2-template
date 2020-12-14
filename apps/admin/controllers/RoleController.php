<?php

namespace admin\controllers;

use Yii;
use admin\models\AdminRole;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 角色管理
 *
 * @auth_key    role
 * @auth_name   角色管理
 */
class RoleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('role'),
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
     * 角色管理列表
     *
     * @auth_key    *
     * @auth_name   查看角色
     * @auth_parent role
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AdminRole::find()->where(['<>', 'status', AdminRole::STATUS_DELETE]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 查看角色详情
     *
     * @auth_key    role_view
     * @auth_name   查看角色
     * @auth_parent role
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
     * 新增活动角色
     *
     * @auth_key    role_create
     * @auth_name   添加角色
     * @auth_parent role
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AdminRole();

        if($model->load(Yii::$app->request->post()) && $model->parseAuth() && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 编辑活动角色
     *
     * @auth_key    role_update
     * @auth_name   更新角色
     * @auth_parent role
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->parseAuth() && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 将角色移动到回收站
     *
     * @auth_key    role_delete
     * @auth_name   删除角色
     * @auth_parent role
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
     * 角色回收站管理
     *
     * @auth_key    role_recycle
     * @auth_name   角色回收站
     * @auth_parent role
     *
     * @return string
     */
    public function actionRecycle()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AdminRole::find()->where(['=', 'status', AdminRole::STATUS_DELETE]),
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 将角色从回收站恢复
     *
     * @auth_key    role_recycle
     * @auth_name   角色回收站
     * @auth_parent role
     *
     * @param $id
     *
     * @return \yii\web\Response
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
     * 彻底删除角色
     *
     * @auth_key    role_recycle
     * @auth_name   角色回收站
     * @auth_parent role
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
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\AdminRole
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = AdminRole::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
