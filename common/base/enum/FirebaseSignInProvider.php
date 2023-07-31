<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class FirebaseSignInProvider
 * @package common\base\enum
 */
class FirebaseSignInProvider extends BaseEnum
{
    const GOOGLE = 'google.com';
    const FACEBOOK = 'facebook.com';
    const PASSWORD = 'password';
    const PHONE = 'phone';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::GOOGLE => Yii::t('enum', 'firebase_sign_in_provider.google.com'),
            self::FACEBOOK => Yii::t('enum', 'firebase_sign_in_provider.facebook.com'),
            self::PASSWORD => Yii::t('enum', 'firebase_sign_in_provider.password'),
            self::PHONE => Yii::t('enum', 'firebase_sign_in_provider.phone'),
        ];
    }
}
