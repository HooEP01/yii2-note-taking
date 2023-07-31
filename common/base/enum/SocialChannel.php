<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class SocialChannel
 * @package common\base\enum
 */
class SocialChannel extends BaseEnum
{
    const FACEBOOK = 'facebook';
    const GOOGLE = 'google';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::FACEBOOK => Yii::t('enum', 'social_channel.facebook'),
            self::GOOGLE => Yii::t('enum', 'social_channel.google'),
        ];
    }
}