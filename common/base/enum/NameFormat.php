<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class NameFormat
 * @package common\base\enum
 */
class NameFormat extends BaseEnum
{
    const FIRST_LAST = '{firstName} {lastName}';
    const LAST_FIRST = '{lastName} {firstName}';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::FIRST_LAST => '[Given Name] [Family Name]',
            self::LAST_FIRST => '[Family Name] [Given Name]',
        ];
    }
}
