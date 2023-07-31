<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class RateType
 * @package common\base\enum
 */
class RateType extends BaseEnum
{
    const NONE = 'none';
    const AMOUNT = 'amount';
    const PERCENT = 'percent';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::NONE => \Yii::t('enum', 'rate.type.none'),
            self::AMOUNT => \Yii::t('enum', 'rate.type.amount'),
            self::PERCENT => \Yii::t('enum', 'rate.type.percent'),
        ];
    }
}