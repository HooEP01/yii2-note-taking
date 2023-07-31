<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=pgsql;dbname=hustle_hero',
            'username' => 'hustle',
            'password' => '12345678',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
        'jwt' => [
            'class' => 'common\base\jwt\Jwt',
            'key' => 'IWMjt13y3j@K7t#Z%9yL7GEs5IJc!gRRAL!#X$zx354t#S%Zu$*35^!b98AX&S&6Rr8T$RkeJpDi4*jbD$O#QOqs^5#Td8',
        ],
        'firebase' => require __DIR__ . '/firebase.php',
    ],
];
