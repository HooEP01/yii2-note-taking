<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use Yii;

/**
 * Class Url
 * @package common\base\helpers
 */
class Url extends \yii\helpers\Url
{
    /**
     * @param array|string $route
     * @param bool $scheme
     * @return string
     */
    public static function toRoute($route, $scheme = false)
    {
        $enforceHttps = static::getIsEnforceHttps();

        if ($scheme === true && $enforceHttps) {
            $scheme = 'https';
        }

        return parent::toRoute($route, $scheme);
    }

    /**
     * @inheritDoc
     */
    public static function ensureScheme($url, $scheme)
    {
        $enforceHttps = static::getIsEnforceHttps();

        if ($scheme === true && $enforceHttps) {
            $scheme = 'https';
        }

        return parent::ensureScheme($url, $scheme);
    }

    /**
     * @inheritDoc
     */
    public static function normalizeRoute($route)
    {
        return parent::normalizeRoute($route);
    }

    /**
     * @return bool
     */
    protected static function getIsEnforceHttps()
    {
        $enforceHttps = YII_ENV_PROD;
        if (isset(Yii::$app->params['https.enforce'])) {
            $enforceHttps = (bool) Yii::$app->params['https.enforce'];
        }
        return $enforceHttps;
    }
}
