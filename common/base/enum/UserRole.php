<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class UserRole
 * @package common\base\enum
 */
class UserRole extends BaseEnum
{
    const SUPER_ADMIN = 'superAdmin';
    const ADMIN = 'admin';
    const STAFF = 'staff';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::SUPER_ADMIN => Yii::t('enum', 'user.role.super_admin'),
            self::ADMIN => Yii::t('enum', 'user.role.admin'),
            self::STAFF => Yii::t('enum', 'user.role.staff'),
        ];
    }

    /**
     * @return array
     */
    public static function getSystemAdminRoles()
    {
        return [
            'systemAdmin',
            self::SUPER_ADMIN,
            self::ADMIN,
            self::STAFF,
        ];
    }
}
