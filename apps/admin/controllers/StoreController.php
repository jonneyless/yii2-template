<?php

namespace admin\controllers;

use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use admin\models\Store;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * 店铺管理类
 *
 * @auth_key  store
 * @auth_name 店铺管理
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
                'rules' => $this->getRules('store'),
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
     * 店铺列表
     *
     * @auth_key    *
     * @auth_parent store
     *
     * @return string
     */
    public function actionIndex()
    {
        if($this->store_id){
            return $this->redirect(['view', 'id' => $this->store_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Store::find()->where(['status' => Store::STATUS_ACTIVE]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 店铺详情
     *
     * @auth_key    store_view
     * @auth_name   查看店铺
     * @auth_parent store
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        if($this->store_id){
            $id = $this->store_id;
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 添加店铺
     *
     * @auth_key    store_create
     * @auth_name   添加店铺
     * @auth_parent store
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Store();
        $model->status = Store::STATUS_ACTIVE;

        if($model->load(Yii::$app->request->post())){
            $preview = UploadedFile::getInstance($model, 'preview');
            if($preview){
                $model->preview = File::newFile($preview->getExtension());
            }

            if($model->validate() && $model->save()){
                if($preview){
                    $preview->saveAs(Folder::getStatic($model->preview));
                }

                return $this->redirect(['view', 'id' => $model->store_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑店铺
     *
     * @auth_key    store_update
     * @auth_name   编辑店铺
     * @auth_parent store
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if($this->store_id){
            $id = $this->store_id;
        }

        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            $preview = UploadedFile::getInstance($model, 'preview');
            if($preview){
                $model->preview = File::newFile($preview->getExtension());
            }

            if($model->validate() && $model->save()){
                if($preview){
                    $preview->saveAs(Folder::getStatic($model->preview));
                }

                return $this->redirect(['view', 'id' => $model->store_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 移除店铺
     *
     * @auth_key    store_remove
     * @auth_name   移除店铺
     * @auth_parent store
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
     * 店铺回收站
     *
     * @auth_key    store_recycle
     * @auth_name   店铺回收站
     * @auth_parent store
     *
     * @return string
     */
    public function actionRecycle()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Store::find()->where(['status' => Store::STATUS_DELETE]),
        ]);

        return $this->render('recycle', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 恢复店铺
     *
     * @auth_key    store_recycle
     * @auth_name   店铺回收站
     * @auth_parent store
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
     * 删除店铺
     *
     * @auth_key    store_recycle
     * @auth_name   店铺回收站
     * @auth_parent store
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
     * @return \admin\models\Store
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Store::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
