<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class PublisherStatus
 * @package app\base\enum
 */
class PublisherStatus extends BaseEnum
{
    const ACTIVE = 'active';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ACTIVE => Yii::t('enum', 'status.active'),
        ];
    }
}
