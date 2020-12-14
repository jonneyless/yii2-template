<?php
namespace wap\controllers;

use ijony\helpers\Url;
use libs\SMS;
use libs\Utils;
use wap\models\Area;
use wap\models\User;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;

/**
 * Ajax controller
 */
class AjaxController extends Controller
{

    public $enableCsrfValidation = false;

    public function init()
    {
        parent::init();

        Yii::$app->response->format = 'json';
    }

    public function actionUrl()
    {
        $params = Yii::$app->request->post('params');
        $params = Json::decode($params);

        return [
            'url' => Url::getFull(Url::to($params)),
        ];
    }

    public function actionArea()
    {
        return [
            'data' => Area::getData()
        ];
    }

    public function actionVcode()
    {
        $mobile = Yii::$app->request->post('mobile');
        $event = Yii::$app->request->post('event');

        try{
            if(!Utils::checkMobile($mobile)){
                throw new ErrorException('手机号码格式不正确。');
            }

            if($event === 'signup' && User::find()->where(['mobile' => $mobile])->exists()){
                return [
                    'error' => 1,
                    'msg' => '手机号码已注册。',
                    'url' => Url::to(['system/download']),
                ];
            }

            $vcode = Yii::$app->cache->get('wap_vcode_' . $mobile);
            if($vcode){
                throw new ErrorException('请不要重复发送！');
            }

            $vcode = rand(100000, 999999);
            $content = "您的验证码为：{$vcode}。半小时内有效。";
            $result = SMS::send($mobile, $content);
            $result = Json::decode($result);

            if(!isset($result['code'])){
                throw new BadRequestHttpException('发送失败！');
            }

            if($result['code'] != 0){
                throw new BadRequestHttpException($result['errorMsg']);
            }

            Yii::$app->cache->set('wap_vcode_' . $mobile, $vcode, 1800);

            return [
                'error' => 0,
                'msg' => '验证码已发送',
            ];
        }catch(ErrorException $e){
            return [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
        }
    }
}
