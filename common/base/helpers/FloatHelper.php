<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

/**
 * Class FloatHelper
 * @package common\base\helpers
 */
class FloatHelper
{
    const THRESHOLD = 0.00001;

    /**
     * @param float $value
     * @return bool
     */
    public static function isZero($value)
    {
        return ($value >= 0 && $value < static::THRESHOLD) || ($value <= 0 && $value > static::THRESHOLD * -1.0000);
    }

    /**
     * @param float $value1
     * @param float $value2
     * @return bool
     */
    public static function isEqual($value1, $value2)
    {
        $different = (float) $value1 - (float) $value2;
        $different = abs($different);

        return $different < static::THRESHOLD;
    }

    /**
     * @param float $value1
     * @param float $value2
     * @return bool
     */
    public static function isGreater($value1, $value2)
    {
        $different = (float) $value1 - (float) $value2;
        return $different > static::THRESHOLD;
    }

    /**
     * @param float $value1
     * @param float $value2
     * @return bool
     */
    public static function isLesser($value1, $value2)
    {
        $different = (float) $value1 - (float) $value2;
        return $different < (static::THRESHOLD * -1.0000);
    }

    /**
     * @param float $value
     * @return bool
     */
    public static function isGreaterThanZero($value)
    {
        return static::isGreater($value, 0);
    }

    /**
     * @param float $value
     * @return bool
     */
    public static function isLesserThanZero($value)
    {
        return static::isLesser($value, 0);
    }

    /**
     * @param float $value1
     * @param float $value2
     * @return bool
     */
    public static function isGreaterOrEqual($value1, $value2)
    {
        return static::isGreater($value1, $value2) || static::isEqual($value1, $value2);
    }

    /**
     * @param float $value1
     * @param float $value2
     * @return bool
     */
    public static function isLesserOrEqual($value1, $value2)
    {
        return static::isLesser($value1, $value2) || static::isEqual($value1, $value2);
    }
}
