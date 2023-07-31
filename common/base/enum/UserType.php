<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class UserType
 * @package common\base\enum
 */
class UserType extends BaseEnum
{
    const ACCOUNT = 'account';
    const CUSTOMER = 'customer';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ACCOUNT => Yii::t('enum', 'user.type.account'),
            self::CUSTOMER => Yii::t('enum', 'user.type.customer'),
        ];
    }
}
