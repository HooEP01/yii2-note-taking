<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use common\base\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use Yii;

/**
 * Class ErrorHandler
 * @package api\base\rest
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @var string
     */
    public $errorAction = 'site/error';

    /**
     * @var string
     */
    public $errorView = '@api/base/views/error.php';

    /**
     * Logs the given exception
     * @param \Exception $exception the exception to be logged
     * @since 2.0.3 this method is now public.
     */
    public function logException($exception)
    {
        $category = get_class($exception);
        if ($exception instanceof HttpException) {
            $category = 'yii\\web\\HttpException:' . $exception->statusCode;
        } elseif ($exception instanceof \ErrorException) {
            $category .= ':' . $exception->getSeverity();
        }
        Yii::error($exception, $category);
    }

    /**
     * @inheritdoc
     */
    protected function handleFallbackExceptionMessage($exception, $previousException)
    {
        if (YII_DEBUG) {
            $currentError = 'An Error occurred while handling another error: ' . (string) $exception;
            $previousError = 'Previous exception: ' . (string) $previousException;

            $data = [
                'success' => false,
                'code' => 500,
                'messages' => [
                    ['type' => 'error', 'text' => $currentError],
                    ['type' => 'error', 'text' => $previousError],
                ],
            ];
        } else {
            $data = [
                'success' => false,
                'code' => 500,
                'messages' => [
                    ['type' => 'error', 'text' => 'An internal server error occurred.'],
                ],
            ];
        }

        header('Content-Type: application/json');
        $json = Json::encode($data);
        echo $json;

        $msg = "An Error occurred while handling another error:\n";
        $msg .= (string) $exception;
        $msg .= "\nPrevious exception:\n";
        $msg .= (string) $previousException;
        $msg .= "\n\$_SERVER = " . VarDumper::export($_SERVER);
        error_log($msg);

        if (defined('HHVM_VERSION')) {
            flush();
        }

        exit(1);
    }
}
