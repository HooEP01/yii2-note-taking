<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class ContactType
 * @package common\base\enum
 */
class ContactType extends BaseEnum
{
    const MOBILE_PHONE = 'mobile-phone';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::MOBILE_PHONE => 'Mobile Phone',
        ];
    }
}
