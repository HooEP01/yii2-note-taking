<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;
use yii\base\Exception;

/**
 * Class SubscriptionCode
 * @package common\base\enum
 */
class SubscriptionCode extends BaseEnum
{
    const AGENCY_30_DAYS_TRIAL = 'agency-trial-30-days';
    const DEVELOPER_30_DAYS_TRIAL = 'developer-trial-30-days';
    const AGENCY_LIFETIME = 'agency-lifetime';
    const DEVELOPER_LIFETIME = 'developer-lifetime';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::AGENCY_30_DAYS_TRIAL => Yii::t('enum', 'subscription_code.agency-trial-30-days'),
            self::DEVELOPER_30_DAYS_TRIAL => Yii::t('enum', 'subscription_code.developer-trial-30-days'),
            self::AGENCY_LIFETIME => Yii::t('enum', 'subscription_code.agency-lifetime'),
            self::DEVELOPER_LIFETIME => Yii::t('enum', 'subscription_code.developer-lifetime'),
        ];
    }

    /**
     * @return array
     */
    public static function agencyOptions()
    {
        return [
            self::AGENCY_30_DAYS_TRIAL => Yii::t('enum', 'subscription_code.agency-trial-30-days'),
            self::AGENCY_LIFETIME => Yii::t('enum', 'subscription_code.agency-lifetime'),
        ];
    }

    /**
     * @return array
     */
    public static function developerOptions()
    {
        return [
            self::DEVELOPER_30_DAYS_TRIAL => Yii::t('enum', 'subscription_code.developer-trial-30-days'),
            self::DEVELOPER_LIFETIME => Yii::t('enum', 'subscription_code.developer-lifetime'),
        ];
    }

    /**
     * @param string $code
     * @return int
     */
    public static function resolveTotalDays($code)
    {
        switch ($code) {
            case self::AGENCY_30_DAYS_TRIAL:
            case self::DEVELOPER_30_DAYS_TRIAL:
                return 30;
                break;
            case self::AGENCY_LIFETIME:
            case self::DEVELOPER_LIFETIME:
                return 365000; // 1,000 years
                break;
        }

        throw new Exception('Invalid Code: ' . $code);
    }
}
