<?php

namespace admin\controllers;

use admin\models\Product;
use admin\models\search\Product as ProductSearch;
use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * 产品管理类
 *
 * @property $product
 *
 * @auth_key    product
 * @auth_name   产品管理
 */
class ProductController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('product'),
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
     * 产品列表
     *
     * @auth_key    *
     * @auth_parent product
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch([
            'status' => [Product::STATUS_UNACTIVE, Product::STATUS_ACTIVE],
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 产品详情
     *
     * @auth_key    product_view
     * @auth_name   产品详情
     * @auth_parent product
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * 添加产品
     *
     * @auth_key    product_create
     * @auth_name   添加产品
     * @auth_parent product
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();
        $model->status = Product::STATUS_ACTIVE;

        if($model->load(Yii::$app->request->post())){
            $preview = UploadedFile::getInstance($model, 'preview');
            if($preview){
                $model->preview = File::newFile($preview->getExtension());
            }

            if($model->save()){
                if($preview){
                    $preview->saveAs(Folder::getStatic($model->preview));
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑产品
     *
     * @auth_key    product_update
     * @auth_name   编辑产品
     * @auth_parent product
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
            $preview = UploadedFile::getInstance($model, 'preview');
            if($preview){
                $model->preview = File::newFile($preview->getExtension());
            }

            if($model->save()){
                if($preview){
                    $preview->saveAs(Folder::getStatic($model->preview));
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 移除产品
     *
     * @auth_key    product_remove
     * @auth_name   移除产品
     * @auth_parent product
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
     * 产品回收站
     *
     * @auth_key    product_recycle
     * @auth_name   产品回收站
     * @auth_parent product
     *
     * @return string
     */
    public function actionRecycle()
    {
        $searchModel = new ProductSearch([
            'status' => Product::STATUS_DELETE
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->store_id);

        return $this->render('recycle', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 恢复产品
     *
     * @auth_key    product_recycle
     * @auth_name   产品回收站
     * @auth_parent product
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
     * 删除产品
     *
     * @auth_key    product_delete
     * @auth_name   删除产品
     * @auth_parent product
     *
     * @param $id
     *
     * @return string
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
     * @return \admin\models\Product
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Product::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
