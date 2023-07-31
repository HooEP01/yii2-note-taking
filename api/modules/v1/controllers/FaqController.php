<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\Controller;
use common\models\Faq;
use yii\data\ActiveDataProvider;

/**
 * Class FaqController
 * @package api\modules\v1\controllers
 */
class FaqController extends Controller
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return ['general'];
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'general' => ['GET']
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionGeneral()
    {
        $query = Faq::find()->general()->active()->orderByDefault();
        return new ActiveDataProvider(['query' => $query]);
    }
}