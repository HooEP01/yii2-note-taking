<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use common\models\Country;
use Yii;

/**
 * Class CountryCode
 * @package common\base\enum
 */
class CountryCode extends BaseEnum
{
    const MALAYSIA = 'MY';

    /**
     * @return array
     */
    public static function options()
    {
        return Country::options();
    }
}