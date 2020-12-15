<?php
return [
    'id' => 'app-admin-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../admin/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
    ],
];
