<?php

namespace admin\controllers;

use ijony\helpers\Image;
use Yii;
use admin\models\Comment;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * 评论管理类
 *
 * @auth_key    comment
 * @auth_name   评论模板管理
 */
class CommentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('comment'),
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
     * 评论列表
     *
     * @auth_key    *
     * @auth_parent comment
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'comment_id' => SORT_DESC,
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加评论
     *
     * @auth_key    comment_create
     * @auth_name   添加评论
     * @auth_parent comment
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $goods_id = Yii::$app->request->getQueryParam('goods_id', '');

        $model = new Comment();
        $model->order_id = '0';
        $model->user_id = 0;
        $model->goods_id = $goods_id;
        $model->goods_score = 5;
        $model->store_score = 5;
        $model->delivery_score = 5;

        if($model->load(Yii::$app->request->post())){
            if($model->save()){
                return $this->redirect(['index']);
            }
        }else{
            foreach($model->images as $image){
                $model->imgs['image'][] = $image->image;
                $model->imgs['thumb'][] = Image::getImg($image->image, 340, 340);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 编辑评论
     *
     * @auth_key    comment_update
     * @auth_name   编辑评论
     * @auth_parent comment
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
            if($model->save()){
                return $this->redirect(['index']);
            }
        }else{
            foreach($model->images as $image){
                $model->imgs['image'][] = $image->image;
                $model->imgs['thumb'][] = Image::getImg($image->image, 340, 340);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除评论
     *
     * @auth_key    comment_delete
     * @auth_name   删除评论
     * @auth_parent comment
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
     * 获取评论对象
     *
     * @param $id
     *
     * @return \admin\models\Comment
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = Comment::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
