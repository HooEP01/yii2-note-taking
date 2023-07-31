<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class AccountUserRole
 * @package common\base\enum
 */
class AccountUserRole extends BaseEnum
{
    const OWNER = 'owner';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::OWNER => Yii::t('enum', 'account_user.role.owner'),
        ];
    }
}
