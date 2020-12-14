<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-wap',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'wap\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-wap',
        ],
        'user' => [
            'identityClass' => 'wap\models\User',
            'enableAutoLogin' => true,
        ],
        'wechat' => [
            'class' => 'maxwen\easywechat\Wechat',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'yii2-mall-wap',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['login'],
                    'logFile' => '@app/runtime/logs/login.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'user/share-<id:\d+>.html' => 'user/share',
                'share-<id:\d+>.html' => 'system/share',
                'promotion-<id:\d+>.html' => 'system/promotion',
                'signup.html' => 'site/signup',
                '/' => 'site/index',
                'user/order/shipping-<id>.html' => 'user/order/shipping',
                '<controller>-<id:\d+>.html' => '<controller>/view',
                '<controller>.html' => '<controller>/index',
                '<controller>/<action>.html' => '<controller>/<action>',
            ]
        ],
        'qrcode' => [
            'class' => '\Da\QrCode\Component\QrCodeComponent',
        ]
    ],
    'params' => $params,
];
