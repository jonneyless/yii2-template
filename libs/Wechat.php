<?php
/**
 * Created by PhpStorm.
 * User: Jony
 * Date: 2017/2/7
 * Time: 16:20
 */

namespace libs;

use Yii;
use yii\helpers\Json;

class Wechat
{

    private static $api_id = '80003527';
    private static $api_secret = '2a35d14172c74c8fd8678031ca8d4450';
    private static $api_token = 'asyouwish';
    private static $appid = 'wx02865bca113114ed';
    private static $token = 'wsdoing1234';
    private static $scope = 'snsapi_userinfo';

    public static function getCode($url)
    {
        $params = [
            'appid' => self::$appid,
            'redirect_uri' => 'http://9cubic.cn/wxroute/' . Yii::$app->request->getHostName() . $url,
            'response_type' => 'code',
            'scope' => self::$scope,
            'state' => 'szpt',
        ];

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    public static function getFans($openid)
    {
        $params = [
            'apiid' => self::$api_id,
            'apisecret' => self::$api_secret,
            'timestamp' => time(),
            'token' => self::$api_token,
        ];

        $params['sign'] = md5(join("", $params));

        unset($params['apisecret']);
        unset($params['token']);

        $params['appid'] = self::$appid;
        $params['openid'] = $openid;
        $params['token'] = self::$token;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://api.9cubic.cn/v1/getFans');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        $result = Json::decode($result);

        return $result;
    }

    public static function getFansByUnionId($unionid)
    {
        $params = [
            'apiid' => self::$api_id,
            'apisecret' => self::$api_secret,
            'timestamp' => time(),
            'token' => self::$api_token,
        ];

        $params['sign'] = md5(join("", $params));

        unset($params['apisecret']);
        unset($params['token']);

        $params['appid'] = self::$appid;
        $params['unionid'] = $unionid;
        $params['token'] = self::$token;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://api.9cubic.cn/v1/getFansByUnionId');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        $result = Json::decode($result);

        if (isset($result['subscribe'])) {
            return $result['subscribe'];
        }

        return false;
    }

    public static function oauth($code)
    {
        $params = [
            'apiid' => self::$api_id,
            'apisecret' => self::$api_secret,
            'timestamp' => time(),
            'token' => self::$api_token,
        ];

        $params['sign'] = md5(join("", $params));

        unset($params['apisecret']);
        unset($params['token']);

        $params['appid'] = self::$appid;
        $params['token'] = self::$token;
        $params['code'] = $code;
        $params['scope'] = self::$scope;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://api.9cubic.cn/v1/oauth');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = curl_exec($ch);
        $result = Json::decode($result);

        return $result;
    }
}