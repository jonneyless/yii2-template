<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$modules = require __DIR__ . '/modules.php';
$rules = require __DIR__ . '/rules.php';

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => $modules,
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if($response->format == yii\web\Response::FORMAT_JSON){
                    $response->data = \libs\Utils::parseResponseData($response);
                }
            },
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'wechat' => [
            'class' => 'maxwen\easywechat\Wechat',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'yii2-mall-api',
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['pospal'],
                    'logVars' => ['_POST', '_GET'],
                    'logFile' => '@app/runtime/logs/pospal.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['notify'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/notify.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['log'],
                    'logVars' => ['_POST', '_FILES'],
                    'logFile' => '@app/runtime/logs/log.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['pay'],
                    'logFile' => '@app/runtime/logs/pay.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['login'],
                    'logFile' => '@app/runtime/logs/login.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['sms'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/sms.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['debug'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/debug.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'pospal' => [
            'class' => 'libs\pospal\Pospal',
            'apiUrl' => 'https://area11-win.pospal.cn:443/',
            'appId' => '0DB76C52485AC5C1EC45CBA9BEEC1260',
            'appKey' => '68721278932198592',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => $rules,
        ],
    ],
    'params' => $params,
];
