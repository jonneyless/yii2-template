<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true ,
        ],
        'sms' => [
            'class' => 'hustshenl\aliyun\sms\Sms',
            'access_key' => 'LTAIGpvD9h0X7yAw',
            'access_secret' => 'tbvN18Au18McUNhRBLdL2u3O3pGUuZ',
            'sign_name' => '来就省',
        ]
    ],
];
