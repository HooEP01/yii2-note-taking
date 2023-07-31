<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\caching;

use yii\caching\Cache;
use yii\caching\Dependency;

/**
 * Class DateDependency
 * @package common\base\caching
 */
class DateDependency extends Dependency
{
    /**
     * @var string the format to be pass in php date() function
     */
    public $format = 'dh';

    /**
     * Generates the data needed to determine if dependency has been changed.
     * Derived classes should override this method to generate the actual dependency data.
     * @param Cache $cache the cache component that is currently evaluating this dependency
     * @return mixed the data needed to determine if dependency has been changed.
     */
    protected function generateDependencyData($cache)
    {
        return date($this->format);
    }
}
