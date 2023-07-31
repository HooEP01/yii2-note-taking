<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class QrCodeType
 * @package common\base\enum
 */
class QrCodeType extends BaseEnum
{
    const USER = 'user';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::USER => Yii::t('enum', 'qr_code_type.user'),
        ];
    }
}
