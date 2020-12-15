<?php
return [
    'id' => 'app-home-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../home/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
    ],
];
