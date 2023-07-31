<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 *
 * @var $exception HttpException|Exception
 * @var $handler ErrorHandler
 */

use common\base\helpers\Json;
use yii\base\UserException;
use yii\web\ErrorHandler;
use yii\web\HttpException;

if ($exception instanceof HttpException) {
    $code = $exception->statusCode;
} else {
    $code = $exception->getCode();
}
$name = $handler->getExceptionName($exception);
if ($name === null) {
    $name = 'Error';
}
if ($code) {
    $name .= " (#$code)";
}

if ($exception instanceof UserException) {
    $message = $exception->getMessage();
} else {
    $message = 'An internal server error occurred.';
}

$data = [
    'success' => false,
    'code' => $code,
    'messages' => [
        ['type' => 'error', 'text' => $message]
    ],
];

echo Json::encode($data);
