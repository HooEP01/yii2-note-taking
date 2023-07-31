<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class ConfigType
 * @package common\base\enum
 */
class ConfigType extends BaseEnum
{
    const RAW = 'raw';
    const TEXT = 'text';
    const STRING = 'string';
    const INTEGER = 'integer';
    const BOOLEAN = 'boolean';
    const DECIMAL = 'decimal';
    const ARRAY = 'array';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::RAW => 'Text (Html Safe)',
            self::TEXT => 'Text (Required HTML Encode)',
            self::STRING => 'String (Short Text)',
            self::INTEGER => 'Integer (Complete Number)',
            self::BOOLEAN => 'bool (Yes/No)',
            self::DECIMAL => 'Float (Decimal Number)',
            self::ARRAY => 'Array'
        ];
    }
}
