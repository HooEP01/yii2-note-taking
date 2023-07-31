<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\web;

use common\base\helpers\ArrayHelper;
use common\base\helpers\Url;
use common\base\traits\RuntimeCache;
use Yii;

/**
 * Class View
 * @package common\base\web
 */
class View extends \yii\web\View
{
    use RuntimeCache;

    /**
     * @return \common\models\User|mixed|null
     */
    protected function getUser()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            return Yii::$app->user->identity;
        });
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return sprintf('%s.%s', $this->getControllerName(), $this->getActionName());
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $controller = $this->context;
            if ($controller instanceof \yii\base\Controller) {
                return strtolower($controller->id);
            }
            return '*';
        });
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $controller = $this->context;
            if ($controller instanceof \yii\base\Controller) {
                if (isset($controller->action)) {
                    return strtolower($controller->action->id);
                }
            }

            return '*';
        });
    }

    /**
     * @param bool $scheme
     * @return string
     */
    public function getBaseUrl($scheme = false)
    {
        $key = md5(__METHOD__ . (int) $scheme);
        return $this->getOrSetRuntimeData($key, function () use ($scheme) {
            return Url::base($scheme);
        });
    }

    /**
     * @return string
     */
    public function getCanonicalUrl()
    {
        return Url::canonical();
    }

    /**
     * Register the app icons
     * @throws \Exception
     */
    public function registerIcons()
    {
        $params = Yii::$app->params;
        $baseImagePath = $this->getBaseUrl(true) . ArrayHelper::getValue($params, 'web.base.image.path', '/images');

        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/png', 'href' => $baseImagePath . '/icons/favicon-32x32.png']);
        $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '192x192', 'href' => $baseImagePath . '/icons/android-icon-192x192.png']);

        foreach ([57, 60, 72, 76, 114, 120, 144, 152, 180] as $i) {
            $sizes = sprintf('%dx%d', $i, $i);
            $this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => $sizes, 'href' => $baseImagePath . sprintf('/icons/apple-icon-%s.png', $sizes)]);
        }

        foreach ([16, 32, 96] as $i) {
            $sizes = sprintf('%dx%d', $i, $i);
            $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => $sizes, 'href' => $baseImagePath . sprintf('/icons/favicon-%s.png', $sizes)]);
        }

        $this->registerLinkTag(['rel' => 'manifest', 'href' => $baseImagePath . '/icons/manifest.json']);
        $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#ffffff']);
        $this->registerMetaTag(['name' => 'msapplication-TileImage', 'content' => $baseImagePath . '/icons/ms-icon-144x144.png']);
        $this->registerMetaTag(['name' => 'theme-color', 'content' => '#ffffff']);
    }
}