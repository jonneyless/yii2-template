<?php

namespace libs;

use common\models\SMSLog;
use Yii;

/**
 * 短信发送类
 *
 * @package libs
 */
class SMS
{
    /**
     * @var string 网关地址
     */
    private static $api_sms = 'http://smssh1.253.com/msg/send/json';
    private static $api_vcode = 'http://smssh1.253.com/msg/variable/json';
    /**
     * @var string 账号
     */
    private static $acc_sms = 'N3122277';
    /**
     * @var string 密码
     */
    private static $pwd_sms = '4OVEMYA9if4946';
    /**
     * @var bool 回调开关
     */
    private static $callback = true;

    /**
     * 短信发送
     *
     * @param $mobile
     * @param $content
     * @param $needstatus
     *
     * @return string
     */
    public static function send($mobile, $content, $needstatus = 'true')
    {
        $params = [
            'account'  => self::$acc_sms,
            'password' => self::$pwd_sms,
            'msg' => urlencode($content),
            'phone' => $mobile,
            'report' => $needstatus
        ];

        Yii::error(self::$api_sms . "\n" . print_r($params, true), 'sms');

        $result = self::post(self::$api_sms, $params);

        return $result;
    }

    /**
     * POST 请求方法
     *
     * @param $url
     * @param $params
     *
     * @return mixed
     */
    private static function post($url, $params)
    {
        $params = json_encode($params);
        $header =  [
            'Content-Type: application/json; charset=utf-8',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        if(false == $result){
            $result = curl_error($ch);
        }else{
            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(200 != $rsp){
                $result = "请求状态 " . $rsp . " " . curl_error($ch);
            }
        }

        curl_close($ch);

        return $result;
    }
}
