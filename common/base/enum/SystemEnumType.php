<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

use Yii;

/**
 * Class SystemEnumType
 * @package common\base\enum
 */
class SystemEnumType extends BaseEnum
{
    const AMENITY = 'amenity';
    const BUILD_STATUS = 'build-status';

    public static function options()
    {
        return [
            self::AMENITY => Yii::t('enum', 'system_enum_type.amenity'),
            self::BUILD_STATUS => Yii::t('enum', 'system_enum_type.build-status'),
        ];
    }
}