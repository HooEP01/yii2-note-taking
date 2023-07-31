<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue', 'pipeline'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'cache' => 'yii\console\controllers\CacheController',
        'migrate' => 'yii\console\controllers\MigrateController',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/',
            'hostInfo' => 'https://api.hustlehero.com.au',
        ],
    ],
    'params' => $params,
];

if (YII_DEBUG) {
    $config['enableCoreCommands'] = true;
    $config['controllerMap']['fixture'] = [
        'class' => 'yii\console\controllers\FixtureController',
        'namespace' => 'common\fixtures',
    ];
}

return $config;