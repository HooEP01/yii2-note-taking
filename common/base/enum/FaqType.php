<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class FaqType
 * @package common\base\enum
 */
class FaqType extends BaseEnum
{
    const GENERAL = 'general';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::GENERAL => \Yii::t('enum', 'faq.type.general'),
        ];
    }
}