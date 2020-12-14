<?php
$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'wap',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'wap\controllers',
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'session' => array(
            'cookieParams' => ['path' => '/wap/', 'httponly' => true],
        ),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
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
                '/index.html' => 'site/index',
                '/join-<id:\d+>.html' => 'site/join',
                '/goods-<id:\d+>.html' => 'site/goods',
                '/group-<id:\d+>.html' => 'site/group',
                '/order-<id:\w+>.html' => 'site/order',
                '/zyd/home.html' => 'zyd/home',
                '/zyd/group.html' => 'zyd/group',
                '/zyd/login.html' => 'zyd/login',
                '/zyd/logout.html' => 'zyd/logout',
                '/pay-<order:\w+>.html' => 'site/pay',
                '/<action:\w+>.html' => 'site/<action>',
                '/payment-<action:\w+>.html' => 'payment/<action>',
                '/user/group-<id:\d+>.html' => 'user/group-view',
                '/user/order-<id:\d+>.html' => 'user/order-view',
                '/user/<action:\w+>.html' => 'user/<action>',
            ],
        ],
        'alidayu' => [
            'class' => '\cdcchen\yii\alidayu\Client',
            'appKey' => '23717713',
            'appSecret' => 'b0ff6db33bc884d197eca07753c74435',
        ],
    ],
    'params' => $params,
];
