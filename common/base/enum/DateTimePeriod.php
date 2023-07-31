<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class DateTimePeriod
 * @package common\base\enum
 */
class DateTimePeriod extends BaseEnum
{
    const MINUTE = 'minute';
    const HOUR = 'hour';
    const DAY = 'day';
    const MONTH = 'month';
    const YEAR = 'year';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::MINUTE => \Yii::t('enum', 'datetime_period.minute'),
            self::HOUR => \Yii::t('enum', 'datetime_period.hour'),
            self::DAY => \Yii::t('enum', 'datetime_period.day'),
            self::MONTH => \Yii::t('enum', 'datetime_period.month'),
            self::YEAR => \Yii::t('enum', 'datetime_period.year'),
        ];
    }

    /**
     * @param $value
     * @param $period
     * @return string|null
     */
    public static function formatToInterval($value, $period)
    {
        switch ($period) {
            case self::YEAR :
                $format = "P{$value}Y";
                break;
            case self::MONTH :
                $format = "P{$value}M";
                break;
            case self::DAY :
                $format = "P{$value}D";
                break;
            case self::HOUR :
                $format = "PT{$value}H";
                break;
            case self::MINUTE :
                $format = "PT{$value}M";
                break;
            default :
                $format = null;
                break;
        }

        return $format;
    }
}