<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\i18n;

use common\base\enum\CurrencyCode;
use common\base\enum\CurrencyFormat;
use common\base\helpers\ArrayHelper;
use common\base\helpers\IntegerDecimal;
use common\base\helpers\UuidHelper;
use yii\helpers\Html;

/**
 * Class Formatter
 * @package common\base\i18n
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @param string|null $value
     * @return string
     */
    public function asShortUuid($value)
    {
        if (empty($value)) {
            return $this->nullDisplay;
        }

        return UuidHelper::encodeShort($value);
    }
    /**
     * @param string|null $value
     * @return string
     */
    public function asText($value)
    {
        if (empty($value)) {
            return $this->nullDisplay;
        }

        return parent::asText($value);
    }

    /**
     * @param int|float $value
     * @param int $decimals
     * @return string
     */
    public function asPercentage($value, $decimals = 0)
    {
        $value = $value / 100;
        return number_format($value, $decimals, '.', '') . '%';
    }

    /**
     * @param float $value
     * @param string $symbol
     * @return mixed
     */
    public function asTemperature($value, $symbol = '°C')
    {
        $temperature = (float) $value;
        if ($temperature <= 0) {
            return $this->nullDisplay;
        }

        return sprintf('%.1f %s', $temperature, $symbol);
    }

    /**
     * @param array $value
     * @return mixed
     */
    public function asPrettyJsonHtml($value)
    {
        if (empty($value)) {
            return $this->nullDisplay;
        }

        $html = nl2br(json_encode($value, JSON_PRETTY_PRINT));
        return str_replace('  ', '&nbsp&nbsp', $html);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function asPrettyBooleanHtml($value)
    {
        if ((bool) $value === true) {
            return Html::tag('span', $this->booleanFormat[1], ['class' => 'label label-success']) ;
        }
        return Html::tag('span', $this->booleanFormat[0], ['class' => 'label label-danger']);
    }

    /**
     * @param int||IntegerDecimal $value
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function asAccountingPrice($value, $options = [])
    {
        $format = ArrayHelper::getValue($options, 'format', CurrencyFormat::SYMBOL_VALUE);
        $currencySymbol = ArrayHelper::getValue($options, 'currencySymbol');
        if ($value instanceof IntegerDecimal) {
            $value = $value->getIntegerValue();
        }

        $value = (int) $value;
        if (!empty($currencySymbol)) {
            $text = strtr($format, ['{symbol}' => $currencySymbol, '{value}' => $this->asAccountingAmount((int) abs($value), $options)]);
            if ($value < 0) {
                return '-' . $text;
            }

            return $text;
        }

        return $this->asAccountingAmount($value, $options);
    }

    /**
     * @param string $value the value to be formatted.
     * @return string the formatted result.
     */
    public function asString($value)
    {
        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    /**
     * @param int $value
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function asAccountingAmount($value, $options = [])
    {
        $amount = (int) $value;

        $precision = (int) ArrayHelper::getValue($options, 'precision', 2);
        $decimals = (int) ArrayHelper::getValue($options, 'decimals', $precision);

        $number = IntegerDecimal::factoryFromInteger($amount, $precision);

        return number_format($number->getFloatValue(), $decimals);
    }

    /**
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    public function asRoundNumber($value, $decimals = 0)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * @param mixed  $value
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function asPoint($value, $options = [])
    {
        $precision = (int) ArrayHelper::getValue($options, 'precision', 2);
        $decimals = (int) ArrayHelper::getValue($options, 'decimals', $precision);

        if (is_integer($value)) {
            $value = IntegerDecimal::factoryFromInteger($value, $precision);
        }

        $amount = $value->getFloatValue();
        $currencySymbol = ArrayHelper::getValue($options, 'currencySymbol');
        if ($amount < 0) {
            return sprintf('-%s %s', number_format(abs($amount), $decimals), $currencySymbol);
        }

        return sprintf('%s %s', number_format($amount, $decimals), $currencySymbol);
    }
}
