<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base;

use yii\BaseYii;
use yii\di\Container;

require(__DIR__ . '/../../vendor/yiisoft/yii2/BaseYii.php');

/**
 * Yii is a helper class serving common framework functionaries.
 */
class Yii extends BaseYii
{
    /**
     * @var Application the application instance
     */
    public static $app;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require(__DIR__ . '/../../vendor/yiisoft/yii2/classes.php');
Yii::$container = new Container();
