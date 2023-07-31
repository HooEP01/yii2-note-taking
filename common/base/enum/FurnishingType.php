<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class FurnishingType
 * @package common\base\enum
 */
class FurnishingType extends BaseEnum
{
    const UNFURNISHED = 'unfurnished';
    const PARTIALLY_FURNISHED = 'partially-furnished';
    const FULLY_FURNISHED = 'fully-furnished';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::UNFURNISHED => Yii::t('enum', 'furnishing_type.unfurnished'),
            self::PARTIALLY_FURNISHED => Yii::t('enum', 'furnishing_type.partially-furnished'),
            self::FULLY_FURNISHED => Yii::t('enum', 'furnishing_type.fully-furnished'),
        ];
    }
}
