<?php

namespace admin\controllers;

use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use admin\models\Ad;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * 广告管理类
 *
 * @auth_key    ad
 * @auth_name   广告管理
 */
class AdController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('ad'),
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
     * 广告列表
     *
     * @auth_key    *
     * @auth_parent ad
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Ad::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 广告详情
     *
     * @auth_key    ad_view
     * @auth_name   广告详情
     * @auth_parent ad
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
     * 添加广告
     *
     * @auth_key    ad_create
     * @auth_name   添加广告
     * @auth_parent ad
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ad();
        $model->status = Ad::STATUS_ACTIVE;

        if($model->load(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model, 'image');
            if($image){
                $model->image = File::newFile($image->getExtension());
            }

            if($model->validate() && $model->save()){
                if($image){
                    $image->saveAs(Folder::getStatic($model->image));
                }

                return $this->redirect(['index', 'id' => $model->ad_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑广告
     *
     * @auth_key    ad_update
     * @auth_name   编辑广告
     * @auth_parent ad
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model, 'image');
            if($image){
                $model->image = File::newFile($image->getExtension());
            }

            if($model->validate() && $model->save()){
                if($image){
                    $image->saveAs(Folder::getStatic($model->image));
                }

                return $this->redirect(['view', 'id' => $model->ad_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除广告
     *
     * @auth_key    ad_delete
     * @auth_name   删除广告
     * @auth_parent ad
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
     * 获取广告对象
     *
     * @param $id
     *
     * @return \admin\models\Ad
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Ad::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
