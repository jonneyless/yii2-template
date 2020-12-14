<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Order;
use api\models\OrderGoods;
use api\models\Service;
use api\models\ServiceAttachment;
use ijony\helpers\File;
use ijony\helpers\Folder;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class ServiceController extends ApiController
{

    public $modelClass = 'api\models\Service';

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

        unset($actions['create'], $actions['view'], $actions['delete']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = Service::find()->where(['user_id' => Yii::$app->user->id]);

        if(isset($params['status'])){
            $status = Service::parseStatus($params['status']);

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

    public function actionCreate()
    {
        $order_id = Yii::$app->request->getBodyParam('order_id');
        $goods_id = Yii::$app->request->getBodyParam('goods_id');
        $quantity = Yii::$app->request->getBodyParam('quantity');
        $type = Yii::$app->request->getBodyParam('type');
        $memo = Yii::$app->request->getBodyParam('memo');
        $attachs = UploadedFile::getInstancesByName('attachs');

        $order = Order::find()->where(['order_id' => $order_id, 'user_id' => Yii::$app->user->id])->one();

        if(!$order){
            throw new BadRequestHttpException('订单不存在！');
        }

        if(!$order->isDone()){
            throw new BadRequestHttpException('请先确认收货后再申请售后！');
        }

        $goods = OrderGoods::find()->where(['order_id' => $order_id, 'goods_id' => $goods_id])->one();

        if($goods->quantity > $quantity){
            throw new BadRequestHttpException('你申请售后的数量不能超过购买的数量！');
        }

        if(Service::find()->where(['order_id' => $order->order_id, 'goods_id' => $goods->goods_id, 'user_id' => Yii::$app->user->id])->exists()){
            throw new BadRequestHttpException('请不要重复申请！');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $service = new Service();
            $service->service_id = $service->genId();
            $service->order_id = $order->order_id;
            $service->goods_id = $goods->goods_id;
            $service->user_id = Yii::$app->user->id;
            $service->type = Service::parseType($type);
            $service->quantity = $quantity;
            $service->memo = Json::encode(["buyer" => $memo]);

            if(!$service->save()){
                Yii::error($service->getErrors());
                throw new ErrorException('售后申请失败！');
            }

            $goods->service_id = $service->service_id;
            if($service->type == Service::TYPE_CHANGE){
                $goods->delivery_status = OrderGoods::DELIVERY_CHANGE;
            }else{
                $goods->delivery_status = OrderGoods::DELIVERY_REFUND;
            }
            $goods->save();

            foreach($attachs as $attach){
                $model = new ServiceAttachment();
                $model->service_id = $service->service_id;
                $model->type = 'image';
                $model->file = File::newFile($attach->getExtension());
                if($model->save()){
                    $attach->saveAs(Folder::getStatic($model->file));
                }
            }

            $transaction->commit();

            return $service->buildViewData();
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionDelete($id)
    {
        $service = Service::find()->where(['service_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$service){
            throw new BadRequestHttpException('售后单不存在！');
        }

        if(!$service->cancel()){
            throw new BadRequestHttpException('售后单撤销失败！');
        }

        $goods = OrderGoods::find()->where(['order_id' => $service->order_id, 'goods_id' => $service->goods_id])->one();
        $goods->service_id = '';
        $goods->delivery_status = OrderGoods::DELIVERY_DONE;
        $goods->save();

        return [
            'message' => '售后单撤销成功！'
        ];
    }
}