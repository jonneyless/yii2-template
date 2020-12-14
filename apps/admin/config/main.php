<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-admin',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-admin',
        ],
        'user' => [
            'identityClass' => 'admin\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-admin', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'yii2-mall-admin',
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
                    'levels' => ['info'],
                    'categories' => ['pospal'],
                    'logVars' => ['_POST', '_GET'],
                    'logFile' => '@app/runtime/logs/pospal.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['sync'],
                    'logFile' => '@app/runtime/logs/sync.log',
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
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@vendor/jonneyless/yii2-admin-asset/views',
                ],
            ],
        ],
    ],
    'params' => $params,
];
