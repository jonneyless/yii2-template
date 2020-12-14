<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Goods;
use Yii;
use yii\web\BadRequestHttpException;

class GoodsController extends ApiController
{

    public $modelClass = 'api\models\Goods';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $searchModel = new \api\models\search\Goods();
        $dataProvider = $searchModel->search($params);

        $items = array_map(function($data){
            return $data->buildListData();
        }, $dataProvider->getModels());

        return [
            'items' => $items
        ];
    }

    public function actionView($id)
    {
        $goods = Goods::findOne($id);

        if(!$goods){
            throw new BadRequestHttpException('商品不存在！');
        }

        return $goods->buildViewData();
    }
}