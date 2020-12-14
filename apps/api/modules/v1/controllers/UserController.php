<?php

namespace api\modules\v1\controllers;

use api\models\Coupon;
use api\models\Payment;
use api\models\ResetForm;
use api\models\SigninForm;
use api\models\SignupForm;
use api\controllers\ApiController;
use api\models\TradePassForm;
use api\models\User;
use api\models\UserInfo;
use api\models\UserRenew;
use ijony\helpers\File;
use ijony\helpers\Folder;
use libs\Utils;
use libs\SMS;
use Yii;
use yii\base\ErrorException;
use api\filters\QueryParamAuth;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

class UserController extends ApiController
{

    public $modelClass = 'api\models\User';

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class'    => QueryParamAuth::className(),
            'optional' => [
                'signin',
                'signup',
                'vcode',
                'reset'
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view'], $actions['update']);

        return $actions;
    }

    public function actionSignin()
    {
        $model = new SigninForm();
        $model->load(Yii::$app->request->post(), '');

        $user = $model->signin();

        if ($user instanceof IdentityInterface) {
            return $user->getApiAccessToken();
        } else {
            throw new UnauthorizedHttpException(\libs\Utils::paserErrors($model->getFirstErrors()));
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        $model->load(Yii::$app->request->post(), '');

        $user = $model->signup();

        if ($user instanceof IdentityInterface) {
            $info = new UserInfo();
            $info->user_id = $user->user_id;
            $info->save();

            return $user->getApiAccessToken();
        }else{
            throw new BadRequestHttpException(\libs\Utils::paserErrors($model->getFirstErrors()));
        }
    }

    public function actionReset()
    {
        $model = new ResetForm();
        $model->load(Yii::$app->request->post(), '');

        if($model->reset()){
            return [
                'message' => '密码重置成功！',
            ];
        }else{
            throw new BadRequestHttpException(\libs\Utils::paserErrors($model->getFirstErrors()));
        }
    }

    public function actionTradepass()
    {
        $model = new TradePassForm();
        $model->load(Yii::$app->request->post(), '');

        if($model->save()){
            return [
                'message' => '交易密码设置成功！',
            ];
        }else{
            throw new BadRequestHttpException(\libs\Utils::paserErrors($model->getFirstErrors()));
        }
    }

    public function actionVcode()
    {
        $mobile = Yii::$app->request->post('mobile');
        $event = Yii::$app->request->post('event');

        if(!Utils::checkMobile($mobile)){
            throw new BadRequestHttpException('手机号码格式不正确。');
        }

        if($event === 'signup' && User::find()->where(['mobile' => $mobile])->exists()){
            throw new BadRequestHttpException('手机号码已注册。');
        }

        if(in_array($event, ['reset', 'tradepass']) && !User::find()->where(['mobile' => $mobile])->exists()){
            throw new BadRequestHttpException('手机号码不存在。');
        }

        $vcode = rand(100000, 999999);
        try{
            $result = Yii::$app->sms->sendSms($mobile, 'SMS_151770413', ['code' => $vcode]);
        }catch(\Exception $e){
            throw new BadRequestHttpException('发送失败！');
        }

        if($result->Code != 'OK'){
            throw new BadRequestHttpException($result->Message);
        }

        Yii::$app->cache->set('api_vcode_' . $mobile, $vcode, 1800);

        Yii::error($mobile . "|" . $vcode, 'sms');

        return [
            'vcode' => $vcode
        ];
    }

    public function actionDetail()
    {
        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        return $user->buildViewData();
    }

    public function actionModify()
    {
        $birthday = Yii::$app->request->getBodyParam('birthday');
        $gander = Yii::$app->request->getBodyParam('gander');
        $username = Yii::$app->request->getBodyParam('username');
        $avatar = UploadedFile::getInstanceByName('avatar');

        /* @var $user \api\models\User */
        $user = Yii::$app->user->identity;

        if(!$birthday){
            $birthday = date("Y-m-d");
        }

        if(!$gander){
            $gander = 'n';
        }

        if(!$username){
            $username = '会员' . $user->mobile;
        }

        $birthday = strtotime($birthday);
        if($birthday < 0){
            $birthday = 0;
        }
        $user->info->birthday = $birthday;
        $user->info->gander = $gander;
        $user->info->save();

        $user->username = $username;
        if($avatar){
            $user->avatar = File::newFile($avatar->getExtension());
        }
        if($user->save()){
            if($avatar){
                $avatar->saveAs(Folder::getStatic($user->avatar));
            }
        }

        return $user->buildViewData();
    }

    public function actionRenew()
    {
        $month = Yii::$app->request->getBodyParam('month');
        $vips = Yii::$app->params['vip'];

        if(!isset($vips[$month])){
            throw new BadRequestHttpException('参数错误。');
        }

        $amount = $vips[$month]['current'];

        $transaction = Yii::$app->db->beginTransaction();

        try{
            $renew = new UserRenew();
            $renew->renew_id = UserRenew::genId();
            $renew->user_id = Yii::$app->user->id;
            $renew->month = $month;
            $renew->amount = $amount;
            $renew->status = UserRenew::STATUS_NEW;
            $renew->save();

            $payment = new Payment();
            $payment->payment_id = (string)Payment::genId();
            $payment->type = 'renew';
            $payment->user_id = Yii::$app->user->id;
            $payment->amount = $renew->amount;
            $payment->orders = Json::encode([$renew->renew_id]);

            if(!$payment->save()){
                Yii::error($payment->getErrors());
                throw new ErrorException('支付单生成失败！');
            }

            $transaction->commit();

            return [
                'payment_id' => $payment->payment_id,
                'amount'     => sprintf("%.02f", $payment->amount),
                'month'      => $month,
            ];
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionCoupon()
    {
        $coupon = Yii::$app->request->getBodyParam('coupon');

        $model = Coupon::find()->where(['code' => $coupon])->one();

        if(!$model){
            return [
                'error' => '兑换券不存在！'
            ];
        }

        if($model->user_id){
            return [
                'error' => '兑换券已使用！'
            ];
        }


        $transaction = Yii::$app->db->beginTransaction();

        try{
            $model->setUsed();

            $transaction->commit();

            return [
                'message' => '兑换成功！'
            ];
        }catch(ErrorException $e){
            $transaction->rollBack();

            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function actionShare()
    {
        return [
            'url' => Utils::getWapUrl(['user/share', 'id' => Yii::$app->user->id, 'in-app' => true]),
        ];
    }
}