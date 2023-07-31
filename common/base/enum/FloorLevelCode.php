<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class FloorLevelCode
 * @package common\base\enum
 */
class FloorLevelCode extends BaseEnum
{
    const GROUND = 'ground';
    const LOW = 'low';
    const MIDDLE = 'middle';
    const HIGH = 'high';
    const PENTHOUSE = 'penthouse';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::GROUND => Yii::t('enum', 'floor_level_code.ground'),
            self::LOW => Yii::t('enum', 'floor_level_code.low'),
            self::MIDDLE => Yii::t('enum', 'floor_level_code.middle'),
            self::HIGH => Yii::t('enum', 'floor_level_code.high'),
            self::PENTHOUSE => Yii::t('enum', 'floor_level_code.penthouse'),
        ];
    }
}
