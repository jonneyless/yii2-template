<?php

namespace api\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Api base controller
 */
class ApiController extends ActiveController
{

    public $modelClass;

    /**
     * {@inheritdoc}
     *
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function init()
    {
        parent::init();

        if(Yii::$app->request->getIsPost() || Yii::$app->request->getIsPut()){
            $params = Yii::$app->request->getBodyParams();
            $debug = Yii::$app->request->getQueryParam('debug');

            if(!YII_ENV_DEV && count($params) > 1 && $debug != 'jiangzhen07'){
                if(!isset($params['sign'])){
                    Yii::error(Yii::$app->request->getBodyParams());
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    throw new BadRequestHttpException('缺少参数验签。');
                }

                $sign = $params['sign'];
                unset($params['sign']);

                $params = array_map(function($param){
                    if(is_array($param)){
                        sort($param);

                        $param = join("", $param);
                    }

                    return $param;
                }, $params);

                ksort($params);

                if($sign !== md5(join("", $params) . Yii::$app->params['apiKey'])){
                    Yii::error(Yii::$app->request->getBodyParams());
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    throw new ForbiddenHttpException('验签失败。');
                }
            }
        }

        if(API_ENV == 'dev'){
            $host = Yii::$app->request->getHostInfo();
            $host = explode(".", $host);
            $host[1] = 'beta-' . $host[1];
            $host = join(".", $host);
            Yii::$app->request->setHostInfo($host);
        }

        Yii::info(Yii::$app->request->getUrl(), 'log');
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function beforeAction($action)
    {
        $tokenParam = 'access-token';

        $client = Yii::$app->request->getHeaders()->get('x-request-client');

        if ($client == 'wapp') {
            $tokenParam = 'accessToken';
        }

        $token = Yii::$app->request->getQueryParam($tokenParam);

        if($token && Yii::$app->user->getIsGuest()){
            Yii::$app->user->loginByAccessToken($token);
        }

        if(Yii::$app->request->getIsPost() || Yii::$app->request->getIsPut()){
            $params = Yii::$app->request->getBodyParams();
            $params['user_id'] = Yii::$app->user->id;
            Yii::$app->request->setBodyParams($params);
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        /* @var $query \yii\db\ActiveQuery */
        $query = $modelClass::find();

        $sorts = [
            'params'          => $params,
            'enableMultiSort' => true,
        ];

        if(method_exists($modelClass, 'setFilter')){
            $query = $modelClass::setFilter($query, $params);
        }

        if(method_exists($modelClass, 'setSort')){
            $sortAttrs = $modelClass::setSort($query, $params);

            if($sortAttrs){
                $sorts['attributes'] = $sortAttrs;
            }
        }

        if(method_exists($modelClass, 'setListFields')){
            $modelClass::setListFields();
        }

        if(method_exists($modelClass, 'setListExtraFields')){
            $modelClass::setListExtraFields();
        }

        if(isset($params['expand'])){
            $query->joinWith(explode(",", $params['expand']));
        }

        return Yii::createObject([
            'class'      => ActiveDataProvider::className(),
            'query'      => $query,
            'pagination' => [
                'validatePage' => false,
                'params'       => $params,
            ],
            'sort'       => $sorts,
        ]);
    }
}
