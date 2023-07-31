<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class AccountType
 * @package app\base\enum
 */
class AccountType extends BaseEnum
{
    const AGENT = 'agent';
    const DEVELOPER = 'developer';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::AGENT => Yii::t('enum', 'account.type.agent'),
            self::DEVELOPER => Yii::t('enum', 'account.type.developer'),
        ];
    }
}
