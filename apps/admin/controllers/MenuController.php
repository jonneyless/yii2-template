<?php

namespace admin\controllers;

use Yii;
use admin\models\Menu;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 菜单管理类
 *
 * @auth_key    menu
 * @auth_name   菜单管理
 */
class MenuController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('menu'),
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
     * 菜单管理列表
     *
     * @auth_key    *
     * @auth_parent menu
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Menu::find()->where(['status' => Menu::STATUS_ACTIVE])->orderBy(['sort' => SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 查看菜单详情
     *
     * @auth_key    admin_view
     * @auth_name   查看菜单
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
     * 新增菜单
     *
     * @auth_key    admin_create
     * @auth_name   添加菜单
     * @auth_parent admin
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Menu();
        $model->status = Menu::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 编辑菜单
     *
     * @auth_key    admin_update
     * @auth_name   更新菜单
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 将菜单移动到回收站
     *
     * @auth_key    admin_delete
     * @auth_name   删除菜单
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
     * 菜单回收站管理
     *
     * @auth_key    admin_recycle
     * @auth_name   菜单回收站
     * @auth_parent admin
     *
     * @return string
     */
    public function actionRecycle()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Menu::find()->where(['status' => Menu::STATUS_DELETE]),
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 将菜单从回收站恢复
     *
     * @auth_key    admin_recycle
     * @auth_name   菜单回收站
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
     * 彻底删除菜单
     *
     * @auth_key    admin_recycle
     * @auth_name   菜单回收站
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
        $this->findModel($id)->delete();

        return $this->redirect(['recycle']);
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\Menu
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
