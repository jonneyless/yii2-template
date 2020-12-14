<?php

return [
    '/alipay-notify.html' => 'alipay/notify',
    '/wechat-notify.html' => 'wechat/notify',
    '/pospal-notify.html' => 'pospal/notify',
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/address',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/guestbook',
        ],
        'except' => ['update', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/store',
        ],
        'only' => ['index', 'view'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/teacher',
        ],
        'extraPatterns' => [
            'POST {id}' => 'subscribe',
        ],
        'only' => ['index', 'subscribe'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/teacher',
        ],
        'extraPatterns' => [
            'POST auth' => 'auth',
            'GET auths' => 'auths',
        ],
        'only' => ['index', 'auth', 'auths'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/message',
        ],
        'extraPatterns' => [
            'GET list' => 'list',
            'GET count' => 'count',
            'POST {id}' => 'read',
        ],
        'only' => ['index', 'list', 'read', 'count'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/category',
            'v1/goods',
            'v1/area',
        ],
        'except' => ['create', 'update', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user',
        ],
        'extraPatterns' => [
            'POST signin' => 'signin',
            'POST signup' => 'signup',
            'POST vcode' => 'vcode',
            'POST reset' => 'reset',
            'POST renew' => 'renew',
            'POST tradepass' => 'tradepass',
            'POST coupon' => 'coupon',
            'GET' => 'detail',
            'POST' => 'modify',
            'GET share' => 'share',
        ],
        'except' => ['index', 'create', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/order',
        ],
        'extraPatterns' => [
            'PUT {id}' => 'confirm',
            'GET count' => 'count',
        ],
        'except' => ['create', 'update'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/service',
            'v1/user/withdraw',
        ],
        'except' => ['update'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/favorite',
        ],
        'extraPatterns' => [
            'POST check' => 'check',
        ],
        'except' => ['view', 'update', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/comment',
        ],
        'except' => ['update', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/comment',
        ],
        'except' => ['create', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/cart',
        ],
        'extraPatterns' => [
            'POST add' => 'add',
            'POST checkout' => 'checkout',
        ],
        'except' => ['create'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/order',
        ],
        'extraPatterns' => [
            'POST pay' => 'pay',
        ],
        'except' => ['index', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/payment',
        ],
        'extraPatterns' => [
            'POST alipay' => 'alipay',
            'PUT alipay' => 'alipay-verify',
            'POST wechat' => 'wechat',
            'PUT wechat' => 'wechat-verify',
            'POST balance' => 'balance',
        ],
        'except' => ['index', 'create', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/system',
        ],
        'extraPatterns' => [
            'GET version' => 'version',
            'GET faq' => 'faq',
            'GET guide' => 'guide',
            'POST share' => 'share',
            'GET vip' => 'vip',
            'GET vip-detail' => 'vip-detail',
            'GET vip-agreement' => 'vip-agreement',
            'GET agreement' => 'agreement',
        ],
        'except' => ['create', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/reward',
        ],
        'extraPatterns' => [
            'GET month' => 'month',
            'GET detail' => 'detail',
            'GET member' => 'member',
            'GET account' => 'account',
            'POST account' => 'update-account',
        ],
        'except' => ['create', 'update', 'view', 'delete'],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/user/performance',
        ],
        'extraPatterns' => [
            'GET person' => 'person',
            'GET company' => 'company',
            'GET person-list' => 'person-list',
            'GET company-list' => 'company-list',
            'GET child' => 'child',
            'GET child-city' => 'child-city',
            'GET invitation' => 'invitation',
        ],
        'except' => ['create', 'update', 'view', 'delete'],
    ],
];