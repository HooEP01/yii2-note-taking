<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\helpers;

use yii\base\BaseObject;
use yii\base\NotSupportedException;
use Yii;

/**
 * Class IntegerDecimal
 * @property int $precision
 * @property int $integerValue
 * @property float $floatValue
 * @package common\base\helpers
 */
class IntegerDecimal extends BaseObject
{
    /**
     * @var int The precision storing base, between 0 ~ 8
     */
    private $_precision;
    /**
     * @var int The number value in Integer-Decimal Mode e.g. 0.01 = 100, if precision = 2
     */
    private $_integerValue;

    /**
     * @param IntegerDecimal|float|int $value
     * @return $this
     * @throws NotSupportedException
     */
    public function plus($value)
    {
        return $this->performOperationWithNumber($value, '+');
    }

    /**
     * @param IntegerDecimal|float|int $value
     * @return $this
     * @throws NotSupportedException
     */
    public function minus($value)
    {
        return $this->performOperationWithNumber($value, '-');
    }

    /**
     * @param IntegerDecimal|float|int $value
     * @return $this
     * @throws NotSupportedException
     */
    public function multiply($value)
    {
        return $this->performOperationWithNumber($value, '*');
    }

    /**
     * @param IntegerDecimal|float|int $value
     * @return $this
     * @throws NotSupportedException
     */
    public function divide($value)
    {
        return $this->performOperationWithNumber($value, '/');
    }

    /**
     * @return float|mixed
     */
    public function getFloatValue()
    {
        if ($this->precision === 0) {
            return $this->integerValue;
        }

        return round($this->integerValue / pow(10, $this->precision), $this->precision);
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setFloatValue($value)
    {
        if ($this->precision === 0) {
            $this->integerValue = $value;
        }

        $amount = round($value * pow(10, $this->precision));
        $this->integerValue = $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getIntegerValue()
    {
        return (int) $this->_integerValue;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setIntegerValue($value)
    {
        $this->_integerValue = (int) $value;
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     * @throws NotSupportedException
     */
    public function setPrecision($value)
    {
        $value = (int) $value;

        if ($value < 0 || $value > 8) {
            throw new NotSupportedException('Precision not supported: ' . $value);
        }

        if ($this->precision === $value) {
            return $this;
        }

        //-- value change, transform the integer value
        if ($this->integerValue !== 0) {
            $number = static::factoryFromFloat($this->getFloatValue(), $value);
            $this->setIntegerValue($number->getIntegerValue());
        }

        $this->_precision = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return (int) $this->_precision;
    }

    /**
     * @param IntegerDecimal|mixed $value
     * @return static
     * @throws NotSupportedException
     */
    public function generateSamePrecisionNumber($value)
    {
        if (is_scalar($value)) {
            $number = static::factoryFromFloat((float) $value, $this->precision);
        } elseif ($value instanceof static) {
            $number = static::factory($value, $this->precision);
        } else {
            throw new NotSupportedException('Type: ' . gettype($value) . ' is not supported !');
        }

        return $number;
    }

    /**
     * @param IntegerDecimal|mixed $number
     * @param string $operator
     * @return IntegerDecimal
     * @throws NotSupportedException
     */
    public function performOperationWithNumber($number, $operator)
    {
        if (!in_array($operator, ['+', '-', '*', '/'])) {
            throw new NotSupportedException('Operator Not Supported: ' . $operator);
        }

        if (is_scalar($number)) {
            $number = $this->generateSamePrecisionNumber($number);
        }

        if (!($number instanceof IntegerDecimal)) {
            throw new NotSupportedException('Type: ' . gettype($number) . ' is not supported !');
        }

        $precision = $number->precision > $this->precision ? $number->precision : $this->precision;
        if ($operator === '/') {
            $precision = 8;
        }

        $first = static::factoryFromFloat($this->getFloatValue(), $precision);
        $second = static::factoryFromFloat($number->getFloatValue(), $precision);

        if ($operator === '+') {
            $final = $first->getIntegerValue() + $second->getIntegerValue();
            return $this->setPrecision($precision)->setIntegerValue($final);
        }

        if ($operator === '-') {
            $final = $first->getIntegerValue() - $second->getIntegerValue();
            return $this->setPrecision($precision)->setIntegerValue($final);
        }

        if ($operator === '*') {
            $final = $first->getFloatValue() * $second->getFloatValue();
            return $this->setPrecision($precision)->setFloatValue($final);
        }

        if ($operator === '/') {
            if ($second->getIntegerValue() !== 0) {
                $final = $first->getFloatValue() / $second->getFloatValue();
                return $this->setPrecision($precision)->setFloatValue($final);
            }

            Yii::error('Divided by zero found !!! set to very big or very small ?');
            return $this->setPrecision($precision)->setIntegerValue(0);
        }

        throw new NotSupportedException('Operator Not Supported: ' . $operator);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('precision: %d, int: %s, float: %s', $this->precision, number_format($this->integerValue, 0), number_format($this->floatValue, $this->precision));
    }

    /**
     * @param IntegerDecimal $value
     * @param int $precision
     * @return static
     */
    public static function factory(IntegerDecimal $value, $precision = 2)
    {
        if ($value->precision === $precision) {
            return static::factoryFromInteger($value->integerValue, $precision);
        }

        return static::factoryFromFloat($value->floatValue, $precision);
    }

    /**
     * @param int $integerValue
     * @param int $precision
     * @return static
     */
    public static function factoryFromInteger($integerValue, $precision = 2)
    {
        $number = new static(['precision' => $precision]);
        $number->setIntegerValue($integerValue);
        return $number;
    }

    /**
     * @param float $floatValue
     * @param int $precision
     * @return static
     */
    public static function factoryFromFloat($floatValue, $precision = 2)
    {
        $number = new static(['precision' => $precision]);
        $number->setFloatValue($floatValue);
        return $number;
    }
}
