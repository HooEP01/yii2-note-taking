<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

use Yii;

/**
 * Class ListingSection
 * @package common\base\enum
 */
class ListingSection extends BaseEnum
{
    const SALE = 'sale';
    const RENT = 'rent';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::SALE => Yii::t('enum', 'listing.section.sale'),
            self::RENT => Yii::t('enum', 'listing.section.rent'),
        ];
    }
}