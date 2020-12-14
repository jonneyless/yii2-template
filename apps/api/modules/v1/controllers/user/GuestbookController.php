<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\Guestbook;
use Yii;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use yii\web\BadRequestHttpException;

class GuestbookController extends ApiController
{

    public $modelClass = 'api\models\Guestbook';

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
        return [];
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $query = Guestbook::find()->where(['user_id' => Yii::$app->user->id]);

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
            return $data->buildData();
        }, $dataProvider->getModels());
    }

    public function actionView($id)
    {
        $model = Guestbook::find()->where(['user_id' => Yii::$app->user->id, 'guestbook_id' => $id])->one();

        if(!$model){
            throw new BadRequestHttpException('留言不存在！');
        }

        return $model->buildData();
    }

    public function actionCreate()
    {
        $params = Yii::$app->request->getBodyParams();

        $model = new Guestbook();
        $model->load($params, '');

        if(!$model->save()){
            throw new BadRequestHttpException(\libs\Utils::paserErrors($model->getFirstErrors()));
        }

        return $model->buildData();
    }
}