<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\controllers;

use api\base\rest\Controller;
use common\base\DateTime;
use Yii;

/**
 * Class SiteController
 * @package api\controllers
 */
class SiteController extends Controller
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return ['index', 'error', 'alert'];
    }

    public function actionAlert()
    {
        $data = Yii::$app->request->getBodyParams();
        return [
            'method' => Yii::$app->request->method,
            'data' => $data,
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $data = [
            'name' => Yii::$app->name,
            'version' => Yii::$app->getVersion(),
            'datetime' => DateTime::getCurrentDateTime(),
        ];

        if (YII_DEBUG) {
            $data['_debug'] = [
                'environment' => YII_ENV,
                'debug_mode' => YII_DEBUG,
                'datetime' => DateTime::getCurrentDateTime(),
                'language' => Yii::$app->language,
            ];
        }

        return $data;
    }

    /**
     * General error output function
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return ['exception' => $exception];
        }

        return [
            'status' => 400,
            'message' => 'System Error !'
        ];
    }
}
