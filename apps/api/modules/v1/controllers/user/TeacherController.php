<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\filters\QueryParamAuth;
use api\models\Teacher;
use api\models\TeacherAuth;
use api\models\TeacherSubscribe;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UnprocessableEntityHttpException;

class TeacherController extends ApiController
{

    public $modelClass = 'api\models\Teacher';

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

        $query = TeacherSubscribe::find()->where(['user_id' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'validatePage' => false,
                'params' => $params,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return array_map(function ($data) {
            return $data->buildListData();
        }, $dataProvider->getModels());
    }

    public function actionAuth()
    {
        $code = Yii::$app->request->post('code');

        if (!$code) {
            throw new BadRequestHttpException('请填写验证码！');
        }

        $teacher = Teacher::find()->where(['user_id' => Yii::$app->user->id])->one();

        if (!$teacher) {
            throw new UnauthorizedHttpException('你不是老师！');
        }

        $auth = new TeacherAuth();
        $auth->teacher_id = $teacher->id;
        $auth->user_id = Yii::$app->user->id;
        $auth->code = $code;
        $auth->save();

        return [
            'message' => '验证成功！',
        ];
    }

    public function actionAuths()
    {
        $teacher = Teacher::find()->where(['user_id' => Yii::$app->user->id])->one();

        if (!$teacher) {
            throw new UnprocessableEntityHttpException('你不是老师！');
        }

        $auths = TeacherAuth::find()->where(['teacher_id' => $teacher->id])->all();

        $list = array_map(function ($auth) {
            return [
                'code' => $auth->code,
                'date' => date("Y-m-d H:i:s", $auth->created_at),
            ];
        }, $auths);

        return [
            'list' => $list,
        ];
    }
}