<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

/**
 * Class ListingType
 * @package common\base\enum
 */
class ListingType extends BaseEnum
{
    const RESIDENTIAL = 'residential';
    const COMMERCIAL = 'commercial';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::RESIDENTIAL => 'Residential',
            self::COMMERCIAL => 'Commercial',
        ];
    }
}