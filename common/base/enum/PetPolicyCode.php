<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class PetPolicyCode
 * @package common\base\enum
 */
class PetPolicyCode extends BaseEnum
{
    const NOT_ALLOWED = 'not-allowed';
    const DOG_ONLY = 'dog-only';
    const CAT_ONLY = 'cat-only';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::NOT_ALLOWED => Yii::t('enum', 'per_policy.not-allowed'),
            self::DOG_ONLY => Yii::t('enum', 'per_policy.dog-only'),
            self::CAT_ONLY => Yii::t('enum', 'per_policy.cat-only'),
        ];
    }
}
