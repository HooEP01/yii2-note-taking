<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class LeadType
 * @package app\base\enum
 */
class LeadType extends BaseEnum
{
    const AGENT = 'agent';
    const DEVELOPER = 'developer';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::AGENT => Yii::t('enum', 'lead.type.agent'),
            self::DEVELOPER => Yii::t('enum', 'lead.type.developer'),
        ];
    }
}
