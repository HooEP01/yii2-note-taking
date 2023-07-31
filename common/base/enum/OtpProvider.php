<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class OtpProvider
 * @package common\base\enum
 */
class OtpProvider extends BaseEnum
{
    const FIREBASE = 'firebase';
    const SYSTEM = 'system';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::FIREBASE => 'Firebase',
            self::SYSTEM => 'System',
        ];
    }
}