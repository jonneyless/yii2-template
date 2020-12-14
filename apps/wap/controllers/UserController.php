<?php

namespace wap\controllers;

use common\models\Address;
use common\models\Group;
use common\models\Order;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * 用户信息
 *
 * @package wap\controllers
 * @author Jony <jonneyless@163.com>
 * @since 2016.11.21
 */
class UserController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    echo $this->render('/site/error', [
                        'message' => '请先登录！',
                        'url' => ['site/login', 'backUrl' => Url::to(['user/' . $action->id])],
                        'delay' => 3,
                    ]);
                },
            ],
        ];
    }

    /**
     * 我的拼单
     *
     * @param int $status
     *
     * @return string
     */
    public function actionGroup($status = Group::STATUS_OVER)
    {
        $this->backUrl = Url::to(['site/index']);

        $group_ids = Order::find()->select('group_id')->where(['user_id' => Yii::$app->user->id])->column();

        $dataProvider = new ActiveDataProvider([
            'query' => Group::find()->where(['id' => $group_ids, 'status' => $status])->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();

        if (Yii::$app->request->getIsAjax()) {
            $return = [
                'list' => $this->renderPartial('group-list', ['listDatas' => $listDatas]),
                'page' => LinkPager::widget(['pagination' => $listPages]),
            ];

            return Json::encode($return);
        }

        return $this->render('group', [
            'listDatas' => $listDatas,
            'listPages' => $listPages,
            'status' => $status,
        ]);
    }

    /**
     * 拼单详情
     *
     * @param $id
     *
     * @return string
     */
    public function actionGroupView($id)
    {
        $this->bottomBarActive = 'user/group';

        /* @var $model \common\models\Group */
        $model = Group::findOne($id);

        if (!$model) {
            return $this->message('该拼单活动不存在！', ['site/index']);
        }

        return $this->render('group-view', ['model' => $model]);
    }

    /**
     * 我的订单
     *
     * @param string $status
     *
     * @return string
     */
    public function actionOrder($status = '')
    {
        $this->backUrl = Url::to(['site/index']);

        $query = Order::find()->where(['user_id' => Yii::$app->user->id]);

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,
            ],
        ]);

        $listDatas = $dataProvider->getModels();
        $listPages = $dataProvider->getPagination();

        if (Yii::$app->request->getIsAjax()) {
            $return = [
                'list' => $this->renderPartial('order-list', ['listDatas' => $listDatas]),
                'page' => LinkPager::widget(['pagination' => $listPages]),
            ];

            return Json::encode($return);
        }

        return $this->render('order', [
            'listDatas' => $listDatas,
            'listPages' => $listPages,
            'status' => $status,
        ]);
    }

    /**
     * 订单详情
     *
     * @param $id
     *
     * @return string
     */
    public function actionOrderView($id)
    {
        $this->bottomBarActive = 'user/order';

        /* @var $model \common\models\Order */
        $model = Order::findOne($id);

        if (!$model) {
            return $this->message('该订单不存在！', ['site/index']);
        }

        return $this->render('order-view', ['model' => $model]);
    }

    /**
     * 取消订单
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionOrderCancel($id)
    {
        $this->bottomBarActive = 'user/order';

        /* @var $model \common\models\Order */
        $model = Order::findOne($id);

        if (!$model) {
            return $this->message('该订单不存在！', ['site/index']);
        }

        if ($model->is_first == Order::IS_FIRST_YES) {
            $model->group->status = Group::STATUS_CANCEL;
            $model->group->save();
        } else {
            $model->setCancel();
        }

        return $this->redirect(['user/order']);
    }

    /**
     * 添加收货地址
     *
     * @return string|\yii\web\Response
     */
    public function actionCreateAddress()
    {
        /* @var $user \common\models\User */
        $user = Yii::$app->user->identity;

        $model = new Address();
        $model->user_id = Yii::$app->user->id;
        $model->area_id = 1;
        $model->phone = $user->mobile;
        $model->is_default = 1;

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->session->set('address_id', $model->id);

            return $this->redirect(['site/cart']);
        } else {
            if (Yii::$app->request->getReferrer()) {
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
            } else {
                Yii::$app->user->setReturnUrl(Url::to(['user/address']));
            }
        }

        return $this->render('create-address', ['model' => $model]);
    }

    /**
     * 收货地址列表
     *
     * @param int $id
     *
     * @return string|\yii\web\Response
     */
    public function actionAddress($id = 0)
    {
        if ($id) {
            $model = Address::find()->where(['user_id' => Yii::$app->user->id, 'id' => $id])->one();

            if ($model) {
                Yii::$app->session->set('address_id', $model->id);

                return $this->redirect(['site/cart']);
            }
        }

        $listDatas = Address::find()->where(['user_id' => Yii::$app->user->id])->all();

        return $this->render('address', ['listDatas' => $listDatas]);
    }
}
