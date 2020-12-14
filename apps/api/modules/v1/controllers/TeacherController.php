<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Teacher;
use api\models\TeacherSubscribe;
use Yii;
use yii\web\BadRequestHttpException;

class TeacherController extends ApiController
{

    public $modelClass = 'api\models\Teacher';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $params = Yii::$app->getRequest()->getQueryParams();

        $searchModel = new \api\models\search\Teacher();
        $dataProvider = $searchModel->search($params);

        $lng = $searchModel->lng;
        $lat = $searchModel->lat;

        $items = array_map(function ($data) use ($lng, $lat) {
            return $data->buildListData($lng, $lat);
        }, $dataProvider->getModels());

        return [
            'items' => $items,
        ];
    }

    public function actionView($id)
    {
        $teacher = Teacher::findOne($id);

        if (!$teacher) {
            throw new BadRequestHttpException('老师不存在！');
        }

        return $teacher->buildViewData();
    }

    public function actionSubscribe($id)
    {
        $params = [
            'user_id' => Yii::$app->user->id,
            'teacher_id' => $id,
            'status' => TeacherSubscribe::STATUS_APPLY,
        ];

        if (TeacherSubscribe::find()->where($params)->exists()) {
            throw new BadRequestHttpException('请不要重复预约！');
        }

        $params = [
            'user_id' => Yii::$app->user->id,
            'teacher_id' => $id,
            'name' => Yii::$app->request->post('name'),
            'phone' => Yii::$app->request->post('phone'),
            'subscribe_at' => Yii::$app->request->post('subscribe_at'),
            'status' => TeacherSubscribe::STATUS_APPLY,
        ];

        if (!(new TeacherSubscribe($params))->save()) {
            throw new BadRequestHttpException('预约失败！');
        }

        return [
            'message' => '成功预约！',
        ];
    }
}