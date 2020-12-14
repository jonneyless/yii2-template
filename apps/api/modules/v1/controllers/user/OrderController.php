<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Order;
use api\models\Service;
use Yii;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use yii\web\BadRequestHttpException;

class OrderController extends ApiController
{

    public $modelClass = 'api\models\Order';

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

        unset($actions['view'], $actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = Order::find()->where(['user_id' => Yii::$app->user->id]);

        if(isset($params['status'])){
            $status = Order::parseStatus($params['status']);

            if($status !== ''){
                $query->andWhere(['status' => $status]);
            }
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
            return $data->buildListData();
        }, $dataProvider->getModels());
    }

    public function actionView($id)
    {
        $order = Order::find()->where(['order_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$order){
            throw new BadRequestHttpException('订单不存在！');
        }

        return $order->buildViewData();
    }

    public function actionConfirm($id)
    {
        $order = Order::find()->where(['order_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$order){
            throw new BadRequestHttpException('订单不存在！');
        }

        if(!$order->isDelivery()){
            throw new BadRequestHttpException('商品尚未发货！');
        }

        $order->delivery();

        return [
            'message' => '收货成功！'
        ];
    }

    public function actionDelete($id)
    {
        $order = Order::find()->where(['order_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$order){
            throw new BadRequestHttpException('订单不存在！');
        }

        if($order->isPaid()){
            if(!$order->refund()){
                throw new BadRequestHttpException('退款申请失败！');
            }

            return [
                'message' => '退款申请成功！'
            ];
        }

        if($order->isNew()){
            if(!$order->cancel()){
                throw new BadRequestHttpException('订单取消失败！');
            }

            return [
                'message' => '订单取消成功！'
            ];
        }

        throw new BadRequestHttpException('只有新订单或已支付订单可以操作！');
    }

    public function actionCount()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        return [
            'total' => $user->getOrder()->count(),
            'unpaid' => $user->getOrder()->andWhere(['status' => Order::STATUS_NEW])->count(),
            'paid' => $user->getOrder()->andWhere(['status' => Order::STATUS_PAID])->count(),
            'delivery' => $user->getOrder()->andWhere(['status' => Order::STATUS_DELIVERY])->count(),
            'done' => $user->getOrder()->andWhere(['status' => Order::STATUS_DONE])->count(),
            'msg' => 0,
            'service' => $user->getService()->andWhere(['status' => [Service::STATUS_NEW, Service::STATUS_WAITING]])->count(),
        ];
    }
}