<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class LanguageCode
 * @package common\base\enum
 */
class LanguageCode extends BaseEnum
{
    const ENGLISH = 'en';
    const SIMPLIFIED_CHINESE = 'zh';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ENGLISH => Yii::t('enum', 'language.english'),
            self::SIMPLIFIED_CHINESE => Yii::t('enum', 'language.simplified_chinese'),
        ];
    }

    /**
     * @return array
     */
    public static function getSupported()
    {
        return [
            self::ENGLISH,
            self::SIMPLIFIED_CHINESE,
        ];
    }

    /**
     * @return array
     */
    public static function getFlagIcons()
    {
        return [
            self::ENGLISH => 'gb',
            self::SIMPLIFIED_CHINESE => 'cn',
        ];
    }

    /**
     * @param string $value
     * @return string
     */
    public static function resolveIcon($value)
    {
        if ($value === self::ENGLISH) {
            return 'gb';
        }

        if ($value === self::SIMPLIFIED_CHINESE) {
            return 'cn';
        }

        return 'us';
    }

    /**
     * @param string $value
     * @return string
     */
    public static function resolveName($value)
    {
        $maps = LanguageCode::options();
        return isset($maps[$value]) ? $maps[$value] : 'Unknown';
    }

    /**
     * @param string $value
     * @param mixed $default
     * @return string|false
     */
    public static function resolveCode($value, $default = null)
    {
        $languages = static::getSupported();
        foreach ($languages as $supported) {
            if (static::isLanguageSupported($value, $supported)) {
                return $supported;
            }
        }

        if ($default === null) {
            return reset($languages);
        }

        return $default;
    }

    /**
     * @param string $requested
     * @param string $supported
     * @return bool
     */
    public static function isLanguageSupported($requested, $supported)
    {
        $supported = str_replace('_', '-', strtolower($supported));
        $requested = str_replace('_', '-', strtolower($requested));
        return strpos($requested . '-', $supported . '-') === 0;
    }
}