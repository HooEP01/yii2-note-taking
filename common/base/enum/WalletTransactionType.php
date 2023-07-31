<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class WalletTransactionType
 * @package common\base\enum
 */
class WalletTransactionType extends BaseEnum
{
    // Super Admin
    const MANUAL = 'manual';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::MANUAL => Yii::t('enum', 'wallet_transaction_type.manual'),
        ];
    }
}