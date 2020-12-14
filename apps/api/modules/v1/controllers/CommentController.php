<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Comment;
use Yii;
use yii\data\ActiveDataProvider;

class CommentController extends ApiController
{

    public $modelClass = 'api\models\Comment';

    public function actionIndex()
    {
        $goods_id = Yii::$app->request->getQueryParam('goods_id');

        $query = Comment::find()->where(['goods_id' => $goods_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => Yii::$app->getRequest()->getQueryParams(),
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
}