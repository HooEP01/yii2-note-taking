<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class CurrencyFormat
 * @package common\base\enum
 */
class CurrencyFormat extends BaseEnum
{
    const SYMBOL_VALUE = '{symbol} {value}';
    const VALUE_SYMBOL = '{value} {symbol}';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::SYMBOL_VALUE => '[Symbol][Value]',
            self::VALUE_SYMBOL => '[Value][Symbol]',
        ];
    }
}