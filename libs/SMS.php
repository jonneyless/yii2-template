<?php

namespace libs;

use cdcchen\alidayu\ResponseException;
use common\models\SMSLog;
use common\models\User;
use Yii;
use yii\helpers\Json;

/**
 * 短信发送类
 *
 * @package libs
 */
class SMS
{
    /**
     * @var bool 回调开关
     */
    private static $callback = true;

    /**
     * 发送验证码
     *
     * @param str $mobile
     * @param bool $ajax
     *
     * @return array|int
     */
    public static function vcode($mobile, $ajax = false)
    {
        $countDown = self::getCountDown();

        if ($countDown) {
            if ($ajax) {
                return self::callback(0, 0, '你还需要等 ' . $countDown . ' 秒才能再次发送验证码！');
            } else {
                return '你还需要等 ' . $countDown . ' 秒才能再次发送验证码！';
            }
        }

        $vcode = Utils::getRand(6, true);

        /* @var \cdcchen\yii\alidayu\Client $client */
        $client = Yii::$app->get('alidayu');

        try {

            $result = $client->sendSms($mobile, '身份验证', 'SMS_14276746', ['code' => $vcode], '9cubic.com');

            if ($result->getSuccess()) {
                $status = 1;
                $message = '验证码发送成功！';

                Yii::$app->session->set('sms_send', time());
                Yii::$app->session->set('send_vcode', $vcode);
            } else {
                $status = 0;
                $message = '验证码发送失败！';
            }

            if ($ajax) {
                return self::callback($status, $status, $message);
            } else {
                return $status ? '' : $message;
            }
        } catch (ResponseException $e) {

            $status = 0;
            $message = '验证码发送失败！';

            if ($ajax) {
                return self::callback($status, $status, $message);
            } else {
                return $status ? '' : $message;
            }
        }
    }

    /**
     * 发送虚拟卡
     *
     * @param str $mobile
     * @param \common\models\Goods $goods
     *
     * @return array|int
     */
    public static function card($mobile, $goods)
    {
        $sign = '苏州爱拼活动';
        $template = 'SMS_63090315';

        /* @var \cdcchen\yii\alidayu\Client $client */
        $client = Yii::$app->get('alidayu');

        try {
            $result = $client->sendSms($mobile, $sign, $template, [], '9cubic.com');

            if ($result->getSuccess()) {
                return true;
            }
        } catch (ResponseException $e) {
            return false;
        }

        return false;
    }

    /**
     * 发送实物提醒
     *
     * @param str $mobile
     * @param \common\models\Goods $goods
     *
     * @return array|int
     */
    public static function goods($mobile, $goods)
    {
        $sign = '苏州爱拼活动';
        $template = 'SMS_63090315';

        /* @var \cdcchen\yii\alidayu\Client $client */
        $client = Yii::$app->get('alidayu');

        try {
            $result = $client->sendSms($mobile, $sign, $template, [], '9cubic.com');

            if ($result->getSuccess()) {
                return true;
            }
        } catch (ResponseException $e) {
            return false;
        }

        return false;
    }

    /**
     * 回调方法
     *
     * @param int $return
     * @param int $result
     * @param string $message
     * @param string $code
     *
     * @return array|int
     */
    public static function callback($return = 0, $result = 0, $message = '', $code = '')
    {
        $return = ['return' => $return, 'result' => $result, 'message' => $message, 'code' => $code];
        if (!self::$callback) {
            return $return;
        }
        echo Json::encode($return);
        Yii::$app->end();
    }

    /**
     * 短信发送 60 秒倒计时
     *
     * @return int
     */
    public static function getCountDown()
    {
        $countDown = time() - Yii::$app->getSession()->get('sms_send', 0);
        if ($countDown > 60) {
            $countDown = 0;
        } else {
            if ($countDown < 0) {
                $countDown = 60;
            } else {
                $countDown = 60 - $countDown;
            }
        }

        return $countDown;
    }

    /**
     * 获取验证码
     *
     * @return mixed
     */
    public static function getVcode()
    {
        $sendTime = Yii::$app->session->get('sms_send', 0);

        if ($sendTime + 1800 < time()) {
            self::destory();
        }

        return Yii::$app->session->get('send_vcode', '');
    }

    /**
     * 注销
     */
    public static function destory()
    {
        Yii::$app->session->set('sms_send', 0);
        Yii::$app->session->set('send_vcode', '');
    }

    /**
     * 校验验证码
     *
     * @param $str
     *
     * @return string
     */
    public static function validator($str)
    {
        $sendTime = Yii::$app->session->get('sms_send', 0);
        $vcode = Yii::$app->session->get('send_vcode', '');

        if (!$vcode) {
            return '请发送验证码';
        }

        if (!$str) {
            return '验证码不可为空';
        }

        if ($vcode != $str) {
            return '验证码不正确';
        }

        if ($sendTime + 1800 < time()) {
            return '验证码已过期';
        }

        return '';
    }
}
