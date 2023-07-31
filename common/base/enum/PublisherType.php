<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class PublisherType
 * @package common\base\enum
 */
class PublisherType extends BaseEnum
{
    const AGENCY = 'agency';
    const DEVELOPER = 'developer';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::AGENCY => Yii::t('enum', 'publisher_type.agency'),
            self::DEVELOPER => Yii::t('enum', 'publisher_type.developer'),
        ];
    }
}
