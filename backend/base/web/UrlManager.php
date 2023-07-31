<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\web;

use Yii;

/**
 * Class UrlManager
 * @package backend\base\web
 */
class UrlManager extends \yii\web\UrlManager
{
    /**
     * @var bool
     */
    public $enablePrettyUrl = true;

    /**
     * @var bool
     */
    public $showScriptName = false;

    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        $params = (array) $params;
        if (!isset($params['_lang'])) {
            $params['_lang'] = Yii::$app->language;
        }

        return parent::createUrl($params);
    }

    /**
     * @inheritdoc
     */
    public function createAbsoluteUrl($params, $scheme = null)
    {
        $params = (array) $params;
        if (!isset($params['_lang'])) {
            $params['_lang'] = Yii::$app->language;
        }

        return parent::createAbsoluteUrl($params, $scheme);
    }
}
