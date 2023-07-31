<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class SizeMeasurement
 * @package common\base\enum
 */
class SizeMeasurement extends BaseEnum
{
    const SQUARE_FEET = 'sqft';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::SQUARE_FEET => Yii::t('enum', 'size_measurement.square-feet'),
        ];
    }
}
