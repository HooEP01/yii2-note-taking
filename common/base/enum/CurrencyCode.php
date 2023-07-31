<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use common\base\helpers\ArrayHelper;
use common\models\Currency;

/**
 * Class CurrencyCode
 * @package common\base\enum
 */
class CurrencyCode extends BaseEnum
{
    const HUSTLE_POINT = 'HP';
    const AUSTRALIAN_DOLLAR = 'AUD';
    /**
     * @return array
     */
    public static function options()
    {
        return Currency::options();
    }
    /**
     * @return array
     */
    public static function supportedOptions()
    {
        $options = Currency::options();

        $items = [];
        foreach (static::supported() as $code) {
            if (ArrayHelper::keyExists($code, $options)) {
                $items[$code] = ArrayHelper::getValue($options, $code);
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    public static function supported()
    {
        return [
            static::AUSTRALIAN_DOLLAR,
        ];
    }
}
