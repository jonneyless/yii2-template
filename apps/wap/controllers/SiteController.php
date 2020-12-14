<?php
namespace wap\controllers;

use libs\Utils;
use wap\models\Ad;
use wap\models\Goods;
use wap\models\SignupForm;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->where([
                'status' => Goods::STATUS_ACTIVE,
                'is_recommend' => Goods::IS_RECOMMEND_ACTIVE
            ])->orderBy(['created_at' => SORT_DESC]),
        ]);

        $data = $dataProvider->getModels();
        $page = $dataProvider->getPagination()->getPage() + 2;

        if(Yii::$app->request->getIsAjax()){
            Yii::$app->response->format = 'json';

            return [
                'html' => $this->renderPartial('/item/goods', ['data' => $data]),
                'page' => $page,
            ];
        }

        $focus = Ad::find()->where(['status' => Ad::STATUS_ACTIVE, 'type' => Ad::TYPE_FOCUS])->orderBy(['sort' => SORT_DESC])->all();
        $guide = Ad::find()->where(['status' => Ad::STATUS_ACTIVE, 'type' => Ad::TYPE_GUIDE])->orderBy(['sort' => SORT_DESC])->all();
        $hots = Goods::find()->where(['status' => Goods::STATUS_ACTIVE, 'is_hot' => Goods::IS_HOT_ACTIVE])->limit(10)->all();

        return $this->render('index', compact(['focus', 'guide', 'hots', 'data', 'page']));
    }

    public function actionSignup()
    {
        $this->bottomBar = false;

        $model = new SignupForm();

        if($model->load(Yii::$app->request->post()) && $model->signup()){
            Yii::$app->user->loginByAccessToken($model->user->access_token);

            return $this->redirect(['system/download']);
        }

        return $this->render('signup', ['model' => $model]);
    }
}
