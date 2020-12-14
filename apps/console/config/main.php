<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
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
            ],
        ],
        'pospal' => [
            'class' => 'libs\pospal\Pospal',
            'apiUrl' => 'https://area11-win.pospal.cn:443/',
            'appId' => '0DB76C52485AC5C1EC45CBA9BEEC1260',
            'appKey' => '68721278932198592',
        ],
    ],
    'params' => $params,
];
