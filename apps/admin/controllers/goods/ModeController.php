<?php

namespace admin\controllers\goods;

use admin\controllers\Controller;
use admin\models\Goods;
use admin\models\GoodsMode;
use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * 商品货品管理类
 *
 * @property $goods
 *
 * @auth_key    mode
 * @auth_name   货品管理
 * @auth_parent goods
 * @auth_group  mode
 */
class ModeController extends Controller
{

    public $goods;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('goods/mode'),
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
     * 货品列表
     *
     * @auth_key    *
     * @auth_parent mode
     * @auth_parent goods
     * @auth_group  mode
     *
     * @param $id
     *
     * @return string
     */
    public function actionIndex($id)
    {
        $this->goods = Goods::findOne($id);

        if(!$this->goods){
            return $this->message('商品不存在！');
        }

        if(!$this->goods->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => GoodsMode::find()->where(['goods_id' => $this->goods->goods_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加货品
     *
     * @auth_key    mode_create
     * @auth_name   添加货品
     * @auth_parent goods
     * @auth_group  mode
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate($id)
    {
        $this->goods = Goods::findOne($id);

        if(!$this->goods){
            return $this->message('商品不存在！');
        }

        if(!$this->goods->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model = new GoodsMode();
        $model->goods_id = $this->goods->goods_id;

        $mode = GoodsMode::find()->where(['goods_id' => $this->goods->goods_id])->one();
        if($mode){
            $model->name = $mode->name;
        }

        if($model->load(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model, 'image');
            if($image){
                $model->image = File::newFile($image->getExtension());
            }

            if($model->save()){
                if($image){
                    $image->saveAs(Folder::getStatic($model->image));
                }

                return $this->redirect(['index', 'id' => $id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑货品
     *
     * @auth_key    mode_update
     * @auth_name   编辑货品
     * @auth_parent goods
     * @auth_group  mode
     *
     * @param $goods_id
     * @param $value
     *
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($goods_id, $value)
    {
        $this->goods = Goods::findOne($goods_id);

        if(!$this->goods){
            return $this->message('商品不存在！');
        }

        if(!$this->goods->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $model = $this->findModel($this->goods->goods_id, $value);

        if($model->load(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model, 'image');
            if($image){
                $model->image = File::newFile($image->getExtension());
            }

            if($model->save()){
                if($image){
                    $image->saveAs(Folder::getStatic($model->image));
                }

                return $this->redirect(['index', 'id' => $goods_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除货品
     *
     * @auth_key    mode_delete
     * @auth_name   删除货品
     * @auth_parent goods
     * @auth_group  mode
     *
     * @param $goods_id
     * @param $value
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($goods_id, $value)
    {
        $this->goods = Goods::findOne($goods_id);

        if(!$this->goods){
            return $this->message('商品不存在！');
        }

        if(!$this->goods->checkStore($this->store_id)){
            return $this->message('对不起，你没有操作权限！');
        }

        $this->findModel($this->goods->goods_id, $value)->delete();

        return $this->redirect(['index', 'id' => $goods_id]);
    }

    /**
     * 获取分类对象
     *
     * @param $id
     * @param $value
     *
     * @return \admin\models\GoodsMode
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id, $value)
    {
        if(($model = GoodsMode::findOne(['goods_id' => $id, 'value' => $value])) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
