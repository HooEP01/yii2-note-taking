<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use common\models\Country;
use Yii;

/**
 * Class MobilePrefix
 * @package common\base\enum
 */
class MobilePrefix extends BaseEnum
{
    const MALAYSIA = '+60';
    const SINGAPORE = '+65';
    const CHINA = '+86';
    const TAIWAN = '+886';
    const HONG_KONG = '+852';
    const JAPAN = '+81';
    const KOREA = '+82';
    const INDONESIA = '+62';
    const INDIA = '+91';
    const NEW_ZEALAND = '+64';
    const USA_CANADA = '+1';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::MALAYSIA => Yii::t('enum', 'mobile_prefix.malaysia'),
        ];
    }

    /**
     * @return array
     */
    public static function countryMaps()
    {
        $key = 'enum-mobile-prefix-country-maps-' . Yii::$app->language  . '-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $items = [];

            $models = static::loadCountries();
            foreach ($models as $model) {
                $code = '+' . $model->telCode;
                $items[$code] = $model->toArray();
            }

            return $items;
        });
    }

    /**
     * @return Country[]
     */
    public static function loadCountries()
    {
        return Country::find()
            ->active()
            ->withMobilePrefix()
            ->orderByDefault()
            ->all();
    }
}
