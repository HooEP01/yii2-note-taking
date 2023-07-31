<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'hustle-backend',
    'name' => 'Admin Portal',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-hustle-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'class' => 'backend\base\web\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_hustleAdmin', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'hustle-backend',
        ],
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
        'view' => [
            'class' => 'backend\base\web\View',
        ],
        'authManager' => [
            'class' => 'backend\base\rbac\AuthManager',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
            'bundles' => require(__DIR__ . '/asset-bundles.php'),
        ],
        'urlManager' => [
            'class' => 'backend\base\web\UrlManager',
            'rules' => require(__DIR__ . '/url-rules.php'),
        ],
        'formatter' => [
            'class' => 'common\base\i18n\Formatter',
            'defaultTimeZone' => 'Asia/Kuala_Lumpur',
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y h:i:s A',
            'currencyCode' => 'MYR',
        ],
    ],
    'params' => $params,
];
