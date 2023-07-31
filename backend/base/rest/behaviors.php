<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

$behaviors = [
    'corsFilter' => [
        'class' => '\yii\filters\Cors',
        'cors' => [
            'Origin' => ['*'],
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Credentials' => null,
            'Access-Control-Max-Age' => 86400,
            'Access-Control-Expose-Headers' => [],
        ],
    ],
    'contentNegotiator' => [
        'class' => '\yii\filters\ContentNegotiator',
        'formats' => [
            'application/json' => \yii\web\Response::FORMAT_JSON,
        ],
        'languages' => \common\base\enum\LanguageCode::getSupported(),
    ],
    'verbFilter' => [
        'class' => '\api\base\filters\VerbFilter',
    ],
];

return $behaviors;
