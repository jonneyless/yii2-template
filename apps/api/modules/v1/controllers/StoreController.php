<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Store;
use Yii;

class StoreController extends ApiController
{

    public $modelClass = 'api\models\Store';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $searchModel = new \api\models\search\Store();
        $dataProvider = $searchModel->search($params);

        $lng = $searchModel->lng;
        $lat = $searchModel->lat;

        $items = array_map(function($data)use($lng, $lat){
            return $data->buildListData($lng, $lat);
        }, $dataProvider->getModels());

        return [
            'items' => $items
        ];
    }

    public function actionView($id)
    {
        $store = Store::findOne($id);

        if(!$store){
            throw new BadRequestHttpException('店铺不存在！');
        }

        return $store->buildViewData();
    }
}