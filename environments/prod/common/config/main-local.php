<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rds1;dbname=szpt',
            'username' => 'szpt',
            'password' => 'Szpt_44d8',
            'charset' => 'utf8',
            'tablePrefix' => 'pt_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
