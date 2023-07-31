<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class PushNotificationProvider
 * @package common\base\enum
 */
class PushNotificationProvider extends BaseEnum
{
    const ONE_SIGNAL = 'one-signal';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ONE_SIGNAL => Yii::t('enum', 'push_notification_provider.one_signal'),
        ];
    }
}