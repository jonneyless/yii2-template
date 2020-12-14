<?php
/**
 * Created by PhpStorm.
 * User: Jony
 * Date: 2017/12/1
 * Time: 15:30
 */

namespace libs;

use GuzzleHttp\Client;
use ijony\helpers\Url;
use liasica\XingeApp\XingeApp;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;

class Utils extends \ijony\helpers\Utils
{

    /**
     * @param $errors
     *
     * @return array
     */
    public static function paserErrors($errors)
    {
        return current($errors);
    }

    /**
     * @param $response \yii\web\Response
     *
     * @return array
     */
    public static function parseResponseData($response)
    {
        $data = $response->data;
        $code = $response->getStatusCode();
        $message = $response->statusText;

        if($code == 201){
            $code = 200;
            $message = '添加成功';
        }

        $return = [
            'code' => $code,
            'message' => $message,
        ];

        if(isset($data['code']) && $data['code'] && is_integer($data['code'])){
            $return['code'] = $data['code'];
            unset($data['code']);
        }

        if(isset($data['message'])){
            $return['message'] =  $data['message'];
            unset($data['message']);
        }

        if(isset($data['error'])){
            $return['message'] =  $data['error'];
            unset($data['error']);
        }

        if ($return['code'] == 401 && $return['message'] == 'Your request was made with invalid credentials.') {
            $return['message'] = '登录已失效，请重新登录！';
        }

        $return['data'] = $data;

        return $return;
    }

    public static function getWapUrl($params)
    {
        $params = Json::encode($params);

        $result = self::post(Url::getFull('/ajax/url.html', 'wap'), [
            'params' => $params,
        ]);

        return $result['url'];
    }

    public static function post($url, $params)
    {
        $options = [
            'base_uri' => Url::getFull('/', 'wap'),
            'headers' => [
                'User-Agent' => 'Yii2 API',
                'Accept' => "application/json",
            ]
        ];

        $client = new Client($options);

        $result = $client->request('POST', $url, [
            'form_params' => $params
        ]);

        return Json::decode($result->getBody()->getContents());
    }

    public static function api($url, $method, $params)
    {
        $base_uri = Url::getFull('/', 'api');

        if(YII_ENV_DEV){
            $base_uri = 'http://api.shop.beiyindi.cn/';
        }

        $options = [
            'base_uri' => $base_uri,
            'headers' => [
                'User-Agent' => 'Yii2 WAP',
                'Accept' => "application/json",
            ]
        ];

        $client = new Client($options);

        $result = $client->request($method, $url, [
            'form_params' => $params
        ]);

        $result = Json::decode($result->getBody()->getContents());

        if($result['code'] >= 300){
            throw new ErrorException($result['message']);
        }

        return $result['data'];
    }

    public static function parseWapUrl($mode, $id)
    {
        return [$mode . '/index', 'id' => $id];
    }

    public static function xinge($android = true)
    {
        $params = $android ? Yii::$app->params['xinge']['android'] : Yii::$app->params['xinge']['iphone'];
        return new XingeApp($params['access_id'], $params['secret_key']);
    }

    public static function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        //将角度转为狐度
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;

        return $s;
    }

    public static function formatDistance($distance)
    {
        if($distance > 1){
            return round($distance, 1) . 'km';
        }else{
            return round($distance * 1000) . 'm';
        }
    }

    public static function squarePoint($lng, $lat, $distance = 3)
    {
        $earth = 6371;
        $dlng = 2 * asin(sin($distance / (2 * $earth)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance / $earth;
        $dlat = rad2deg($dlat);
        $arr = [
            'lt' => ['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
            'rt' => ['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
            'lb' => ['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
            'rb' => ['lat' => $lat - $dlat, 'lng' => $lng + $dlng],
        ];

        return $arr;
    }
}