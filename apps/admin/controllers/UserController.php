<?php

namespace admin\controllers;

use admin\models\User;
use Yii;
use yii\filters\AccessControl;

/**
 * 会员管理类
 *
 * @auth_key    user
 * @auth_name   会员管理
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('user'),
            ],
        ];
    }

    /**
     * 会员列表
     *
     * @auth_key    *
     * @auth_parent user
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \admin\models\search\User();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 会员列表
     *
     * @auth_key    *
     * @auth_parent user
     *
     * @return string
     */
    public function actionRenew($id)
    {
        $model = User::findOne($id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
            $model->syncUpdate();
            return $this->redirect(['index']);
        }

        return $this->render('renew', [
            'model' => $model,
        ]);
    }

    /**
     * 会员列表
     *
     * @auth_key    *
     * @auth_parent user
     *
     * @return string
     */
    public function actionAgent($id)
    {
        $model = User::findOne($id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('company', [
            'model' => $model,
        ]);
    }

    /**
     * 会员列表
     *
     * @auth_key    *
     * @auth_parent user
     *
     * @return string
     */
    public function actionSync($id)
    {
        $model = User::findOne($id);
        $model->syncUpdate();

        return $this->message('同步完成！');
    }
}
