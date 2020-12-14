<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\UserFavorite;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use Yii;
use yii\helpers\Json;

class FavoriteController extends ApiController
{

    public $modelClass = 'api\models\UserFavorite';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

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

        unset($actions['create']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = UserFavorite::find()->where(['user_id' => Yii::$app->user->id]);

        if(isset($params['type']) && $params['type'] !== ''){
            $query->andWhere(['type' => $params['type']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
        ]);

        $data = array_map(function ($data) {
            return $data->buildData();
        }, $dataProvider->getModels());

        $items = [];

        foreach ($data as $datum) {
            $items[$datum['store_id']]['name'] = $datum['type'] == 0 ? $datum['store_name'] : $datum['name'];

            if (!isset($items[$datum['store_id']]['items'])) {
                $items[$datum['store_id']]['items'] = [];
            }

            if ($datum['type'] == 0) {
                $items[$datum['store_id']]['items'][] = $datum;
            }
        }

        sort($items);

        return [
            'stores' => $items,
        ];
    }

    public function actionCreate()
    {
        $params = [
            'user_id' => Yii::$app->user->id,
            'type' => Yii::$app->request->getBodyParam('type'),
            'relation_id' => Yii::$app->request->getBodyParam('relation_id'),
        ];

        $model = UserFavorite::find()->where($params)->one();

        if($model){
            $model->delete();

            return [
                'message' => '已取消收藏！',
            ];
        }else{
            $model = new UserFavorite();
            $model->setAttributes($params);
            if($model->save()){
                return [
                    'message' => '已成功收藏！',
                ];
            }

            return $model->getErrors();
        }
    }

    public function actionCheck()
    {
        $relation_ids = Yii::$app->request->getBodyParam('relation_ids');
        $type = Yii::$app->request->getBodyParam('type');
        $relation_ids = Json::decode($relation_ids);

        $items = UserFavorite::find()->where(['relation_id' => $relation_ids, 'type' => $type, 'user_id' => Yii::$app->user->id])->indexBy('relation_id')->all();

        $return = [];
        foreach($relation_ids as $relation_id){
            $return[$relation_id] = isset($items[$relation_id]);
        }

        return $return;
    }
}