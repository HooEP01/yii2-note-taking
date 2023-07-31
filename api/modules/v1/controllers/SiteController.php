<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\Controller;
use common\base\DateTime;
use Yii;

/**
 * Class SiteController
 * @package api\modules\v1\controllers
 */
class SiteController extends Controller
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return [
            'index',
        ];
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'index' => ['GET'],
        ];
    }

    /**
     * @return array
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
     * @return array
     */
    public function actionNavigation()
    {
        $dashboard = [
            'id' => 'dashboard',
            'title' => 'Dashboard',
            'type' => 'basic',
            'icon' => 'heroicons_outline:chart-pie',
            'link' => '/dashboard',
        ];

        $unit = [
            'id' => 'unit',
            'title' => 'Units',
            'type' => 'basic',
            'icon' => 'heroicons_outline:office-building',
            'link' => '/unit',
        ];

        $stayPass = [
            'id' => 'stay-pass',
            'title' => 'Stay Pass',
            'type' => 'basic',
            'icon' => 'heroicons_outline:identification',
            'link' => '/stay-pass',
        ];

        return [
            'compact' => [$dashboard],
            'default' => [$dashboard, $unit, $stayPass],
            'futuristic' => [$dashboard],
            'horizontal' => [$dashboard, $unit, $stayPass],
        ];
    }
}