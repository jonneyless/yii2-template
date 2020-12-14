<?php

namespace api\modules\v1\controllers\user;

use api\controllers\ApiController;
use api\models\UserWithdraw;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use api\filters\QueryParamAuth;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class WithdrawController extends ApiController
{

    public $modelClass = 'api\models\UserWithdraw';

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

        $query = UserWithdraw::find()->where(['user_id' => Yii::$app->user->id]);

        if(isset($params['status'])){
            $status = UserWithdraw::parseStatus($params['status']);

            if($status !== ''){
                $query->andWhere(['status' => $status]);
            }
        }

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
            return $data->buildListData();
        }, $dataProvider->getModels());
    }

    public function actionCreate()
    {
        $amount = Yii::$app->request->getBodyParam('amount');
        $type = Yii::$app->request->getBodyParam('type');
        $account = Yii::$app->request->getBodyParam('account');

        if(is_array($account)){
            $account = Json::encode($account);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try{
            Yii::$app->user->identity->updateBalance($amount);

            $withdraw = new UserWithdraw();
            $withdraw->user_id = Yii::$app->user->id;
            $withdraw->amount = $amount;
            $withdraw->type = $type;
            $withdraw->account = $account;

            if(!$withdraw->save()){
                Yii::error($withdraw->getErrors());
                throw new ErrorException('提现申请失败！');
            }

            $transaction->commit();

            Yii::$app->user->identity->syncBalance(-1 * $amount);

            $return = $withdraw->buildViewData();

            $return['balance'] = Yii::$app->user->identity->amount;

            return $return;
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionDelete($id)
    {
        $withdraw = UserWithdraw::find()->where(['withdraw_id' => $id, 'user_id' => Yii::$app->user->id])->one();

        if(!$withdraw){
            throw new BadRequestHttpException('提现申请单不存在！');
        }

        if(!$withdraw->cancel()){
            throw new BadRequestHttpException('提现申请单撤销失败！');
        }

        Yii::$app->user->identity->updateBalance(-1 * $withdraw->amount);
        Yii::$app->user->identity->syncBalance($withdraw->amount);

        return [
            'message' => '提现申请单撤销成功！',
            'balance' => Yii::$app->user->identity->amount,
        ];
    }
}