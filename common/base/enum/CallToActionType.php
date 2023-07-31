<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class CallToActionType
 * @package common\base\enum
 */
class CallToActionType extends BaseEnum
{
    const URL = 'url';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::URL => 'URL (HTTP(s) Link)',
        ];
    }
}
