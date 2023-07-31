<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class AuthIdentityType
 * @package common\base\enum
 */
class AuthIdentityType extends BaseEnum
{
    const NAME = 'name';
    const AVATAR = 'avatar';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const GOOGLE = 'google.com';
    const FACEBOOK = 'facebook.com';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::NAME => Yii::t('enum', 'auth_identity_type.name'),
            self::AVATAR => Yii::t('enum', 'auth_identity_type.avatar'),
            self::EMAIL => Yii::t('enum', 'auth_identity_type.email'),
            self::PHONE => Yii::t('enum', 'auth_identity_type.phone'),
            self::GOOGLE => Yii::t('enum', 'auth_identity_type.google.com'),
            self::FACEBOOK => Yii::t('enum', 'auth_identity_type.facebook.com'),
        ];
    }

    /**
     * @return string[]
     */
    public static function bindable()
    {
        return [
            self::PHONE,
            self::GOOGLE,
            self::FACEBOOK,
        ];
    }
}
