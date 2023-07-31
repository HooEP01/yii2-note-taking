<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

use Yii;

/**
 * Class ListingKind
 * @package common\base\enum
 */
class ListingKind extends BaseEnum
{
    const PROJECT = 'project';
    const PROPERTY = 'property';
    const UNIT_TYPE = 'unit-type';

    //-- internal managed listing (viewable, as statistic), internal mapping to "User Generated Content" Project
    const SYSTEM_PROJECT = 'system-project';
    const USER_PROJECT = 'user-project';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::PROJECT => Yii::t('enum', 'listing.kind.project'),
            self::PROPERTY => Yii::t('enum', 'listing.kind.property'),
            self::UNIT_TYPE => Yii::t('enum', 'listing.kind.unit-type'),

            self::SYSTEM_PROJECT => Yii::t('enum', 'listing.kind.system-project'),
            self::USER_PROJECT => Yii::t('enum', 'listing.kind.user-project'),
        ];
    }
}
