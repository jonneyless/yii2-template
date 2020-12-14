<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Ad;
use api\models\Goods;
use api\models\Page;
use ijony\helpers\Image;
use ijony\helpers\Url;
use libs\Utils;
use Yii;
use yii\web\BadRequestHttpException;

class SystemController extends ApiController
{
    public $modelClass = '';

    public function actionIndex()
    {
        $focus = Ad::find()->where(['status' => Ad::STATUS_ACTIVE, 'type' => Ad::TYPE_FOCUS])->orderBy(['sort' => SORT_DESC])->all();
        $hot = Goods::find()->joinWith('store')->where(['goods.status' => Goods::STATUS_ACTIVE, 'goods.is_hot' => Goods::IS_HOT_ACTIVE])->limit(2)->all();

        $focus = array_map(function($item){
            return [
                'name' => $item->name,
                'image' => Image::getImg($item->image, 0, 0, 'default.jpg'),
                'mode' => $item->mode,
                'url' => $item->url,
            ];
        }, $focus);

        $hot = array_map(function($item){
            return $item->buildListData();
        }, $hot);

        return compact(['focus', 'hot']);
    }

    public function actionVersion()
    {
        return [
            'version' => 'v1',
            'android' => 12,
            'iphone' => '1.3',
            'debug' => YII_ENV_DEV,
        ];
    }

    public function actionAgreement()
    {
        return Page::getPageById(1);
    }

    public function actionFaq()
    {
        return [
            'url' => Url::getFull('system/faq.html', 'wap'),
        ];
    }

    public function actionGuide()
    {
        return [
            'images' => [
                Url::getStatic('guide/2.png'),
                Url::getStatic('guide/3.png'),
                Url::getStatic('guide/1.png'),
            ],
            'last' => 0
        ];
    }

    public function actionVip()
    {
        $vips = Yii::$app->params['vip'];

        $return = [];
        foreach($vips as $month => $vip){
            $vip['month'] = $month;
            $return[] = $vip;
        }

        return $return;
    }

    public function actionVipDetail()
    {
        return [
            'contant' => Url::getStatic('vip.jpg'),
        ];
    }

    public function actionVipAgreement()
    {
        return Page::getPageById(1);
    }

    public function actionShare()
    {
        $id = Yii::$app->request->getBodyParam('id');
        $type = Yii::$app->request->getBodyParam('type');
        $token = Yii::$app->request->getBodyParam('access-token');

        if($type != 'promotion'){
            if(YII_ENV_PROD){
                throw new BadRequestHttpException('功能开发中！');
            }
        }

        Yii::$app->user->loginByAccessToken($token);

        return [
            'title' => '来就省商城',
            'content' => '国内首家会员商城，专注为会员提供货真、优质、低价的商品！',
            'image' => Utils::getWapUrl('/img/logo.png'),
            'url' => Utils::getWapUrl(['system/share', 'id' => Yii::$app->user->id]),
        ];
    }
}
