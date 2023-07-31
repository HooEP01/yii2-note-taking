<?php
return [
    'name' => 'Hustle Hero',
    'version' => 'v1.0.0',
    'timezone' => 'Australia/Melbourne',
    'language' => \common\base\enum\LanguageCode::ENGLISH,
    'sourceLanguage' => \common\base\enum\LanguageCode::ENGLISH,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'jwt' => [
            'class' => 'common\base\jwt\Jwt',
            'key'   => '',
        ],
        'cache' => [
            'class' => 'common\base\caching\MainCache',
            'cache' => 'fileCache',
        ],
        'fileCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/_cache',
        ],
        'redisCache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 15, //prevent flush cache clear queue, redis allow 0-15
                'retries' => 1
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
            'retries' => 1
        ],
        'queue' => [
            'class' => 'common\base\queue\core\Queue',
            'driver' => 'db',
            'driverOptions' => [
                'db' => [
                    'class' => 'common\base\queue\db\Queue',
                    'commandClass' => 'common\base\queue\db\Command',
                    'channel' => 'hustle-hero-queue',
                ],
                'redis' => [
                    'class' => 'yii\queue\redis\Queue',
                    'redis' => 'redis',
                    'channel' => 'hustle-hero-queue',
                    'commandClass' => 'common\base\queue\redis\Command',
                ],
            ],
        ],
        'pipeline' => [
            'class' => 'common\base\queue\core\Queue',
            'driver' => 'db',
            'driverOptions' => [
                'db' => [
                    'class' => 'common\base\queue\db\Queue',
                    'commandClass' => 'common\base\queue\db\Command',
                    'channel' => 'hustle-hero-pipeline',
                ],
                'redis' => [
                    'class' => 'yii\queue\redis\Queue',
                    'redis' => 'redis',
                    'channel' => 'hustle-hero-pipeline',
                    'commandClass' => 'common\base\queue\redis\Command',
                ],
            ],
        ],
        'formatter' => [
            'class' => 'common\base\i18n\Formatter',
            'defaultTimeZone' => 'Australia/Melbourne',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'currencyCode' => 'MYR',
        ],
        'config' => [
            'class' => 'common\base\services\Config',
        ],
        'audit' => [
            'class' => 'common\base\audit\Audit',
        ],
        'platform' => [
            'class' => 'common\base\services\Platform',
        ],
        'sanitizer' => [
            'class' => 'common\base\services\Sanitizer',
            'htmlPurifierOptions' => require __DIR__ . '/html_purifier.php',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => \common\base\enum\LanguageCode::ENGLISH,
                    'forceTranslation' => true,
                ],
            ],
        ],
        'mailer' => [
            'class' => 'common\base\mail\Mailer',
            'view' => [
                'renderers' => [
                    'twig' => [
                        'class' => 'yii\twig\ViewRenderer',
                        'cachePath' => false,
                        'globals' => [
                            'html' => '\yii\helpers\Html',
                            'yii' => 'Yii',
                        ],
                    ],
                ],
            ],
            'htmlLayout' => false,
            'viewPath' => '@common/mail',
            'debugMode' => true,
            'debugEmail' => ['ryu@alpstein.my' => 'RYU Chua']
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'itemFile' => '@common/rbac/items.php',
            'assignmentFile' => '@common/rbac/assignments.php',
            'ruleFile' => '@common/rbac/rules.php',
        ],
        'mutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
            'autoRelease' => false,
        ],
        'dynamoDb' => [
            'class' => 'common\base\services\DynamoDb',
            'tablePrefix' => 'Dev-',
        ],
        'aws' => [
            'class' => 'common\base\services\Aws',
            'region' => '[value]',
            'apiKey' => '[value]',
            'apiSecret' => '[value]',
        ],
        'firebase' => [
            'class' => 'common\base\services\Firebase',
            'credential' => [
                'type' => 'service_account',
                'project_id' => 'hustle-hero-uat',
                'client_id' => '[client-id]',
                'client_email' => '[client-email]',
                'private_key' => '[private-key]',
            ],
        ],
        'qr' => [
            'class' => 'common\base\services\Qr',
            'size' => 512,
        ],
    ],
];
