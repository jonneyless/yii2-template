<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Comment;
use api\models\CommentImage;
use api\models\Order;
use api\models\OrderGoods;
use ijony\helpers\File;
use ijony\helpers\Folder;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class CommentController extends ApiController
{

    public $modelClass = 'api\models\Comment';

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['create'], $actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = OrderGoods::find()->where(['user_id' => Yii::$app->user->id]);

        if(isset($params['status']) && $params['status'] !== ''){
            $status = OrderGoods::parseCommentStatus($params['status']);

            if($status !== ''){
                $query->andWhere(['status' => $status]);
            }
        }else{
            $query->andWhere(['status' => [OrderGoods::STATUS_DONE, OrderGoods::STATUS_COMMENT]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        return array_map(function($data){
            return $data->buildCommentListData();
        }, $dataProvider->getModels());
    }

    public function actionCreate()
    {
        $goods_id = Yii::$app->request->getBodyParam('goods_id');
        $order_id = Yii::$app->request->getBodyParam('order_id');
        $content = Yii::$app->request->getBodyParam('content');
        $goods_score = Yii::$app->request->getBodyParam('goods_score');
        $store_score = Yii::$app->request->getBodyParam('store_score');
        $delivery_score = Yii::$app->request->getBodyParam('delivery_score');
        $images = UploadedFile::getInstancesByName('images');

        $order = Order::find()->where(['order_id' => $order_id, 'user_id' => Yii::$app->user->id])->one();

        if(!$order){
            throw new BadRequestHttpException('订单不存在！');
        }

        if(!$order->isDone()){
            throw new BadRequestHttpException('请先确认收货后再申请售后！');
        }

        $goods = OrderGoods::find()->where(['order_id' => $order_id, 'goods_id' => $goods_id])->one();

        if($goods->isComment()){
            throw new BadRequestHttpException('请不要重复评价！');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $comment = new Comment();
            $comment->order_id = $order_id;
            $comment->goods_id = $goods_id;
            $comment->user_id = Yii::$app->user->id;
            $comment->goods_score = intval($goods_score);
            $comment->store_score = intval($store_score);
            $comment->delivery_score = intval($delivery_score);
            $comment->content = $content;

            if(!$comment->save()){
                Yii::error($comment->getErrors());
                throw new BadRequestHttpException('提交评价失败！');
            }

            if(!$goods->comment()){

                Yii::error($goods->getErrors());
            }

            foreach($images as $image){
                $model = new CommentImage();
                $model->comment_id = $comment->comment_id;
                $model->image = File::newFile($image->getExtension());
                if($model->save()){
                    $image->saveAs(Folder::getStatic($model->image));
                }
            }

            $transaction->commit();

            return [
                'message' => '谢谢您的评价！',
            ];
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionView($id)
    {
        $comment = Comment::find()->where(['comment_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$comment){
            throw new BadRequestHttpException('评价不存在！');
        }

        return $comment->buildViewData();
    }
}