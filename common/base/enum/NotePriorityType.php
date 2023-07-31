<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class NotePriorityType
 * @package common\base\enum
 */
class NotePriorityType extends BaseEnum
{
    const CRITICAL = 'critical';
    const MAJOR = 'major';

    const MODERATE = 'moderator';

    const LOW = 'low';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::CRITICAL => Yii::t('enum', 'critical'),
            self::MAJOR => Yii::t('enum', 'major'),
            self::MODERATE => Yii::t('enum', 'moderate'),
            self::LOW => Yii::t('enum', 'low'),
        ];
    }
}
