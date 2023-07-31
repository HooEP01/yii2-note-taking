<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class TenureCode
 * @package common\base\enum
 */
class TenureCode extends BaseEnum
{
    const FREEHOLD = 'freehold';
    const LEASEHOLD = 'leasehold';
    const BUMI_LOT = 'bumi-lot';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::FREEHOLD => Yii::t('enum', 'tenure_code.freehold'),
            self::LEASEHOLD => Yii::t('enum', 'tenure_code.leasehold'),
            self::BUMI_LOT => Yii::t('enum', 'tenure_code.bumi-lot'),
        ];
    }
}
