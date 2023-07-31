<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'hustle-api',
    'name' => 'Hustle Hero API',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => 'api\modules\v1\Module',
    ],
    'components' => [
        'user' => [
            'class' => 'api\base\web\User',
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'enableAutoLogin' => false,
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'charset' => 'UTF-8',
            'as api' => [
                'class' => 'api\base\behaviors\ApiResponseBehavior',
            ],
        ],
        'errorHandler' => [
            'class' => 'api\base\rest\ErrorHandler',
            'errorAction' => 'site/error',
            'errorView' => '@api/base/views/error.php',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => require(__DIR__ . '/url-rules.php'),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'error' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:4*',
                        'yii\i18n\PhpMessageSource*',
                    ],
                ],
                'error-4xx' =>[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/error-4xx.log',
                    'categories' => [
                        'yii\web\HttpException:4*',
                    ],
                    'except' => [
                        'yii\web\HttpException:401',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
