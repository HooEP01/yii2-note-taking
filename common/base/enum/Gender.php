<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class Gender
 * @package common\base\enum
 */
class Gender extends BaseEnum
{
    const MALE = 'male';
    const FEMALE = 'female';
    const OTHER = 'other';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::FEMALE => Yii::t('enum', 'gender.female'),
            self::MALE => Yii::t('enum', 'gender.male'),
            self::OTHER => Yii::t('enum', 'gender.other'),
        ];
    }

    /**
     * @return array
     */
    public static function descriptiveOptions()
    {
        return [
            self::FEMALE => Yii::t('enum', 'gender.female'),
            self::MALE => Yii::t('enum', 'gender.male'),
            self::OTHER => Yii::t('enum', 'gender.other_or_decline_to_state'),
        ];
    }
}