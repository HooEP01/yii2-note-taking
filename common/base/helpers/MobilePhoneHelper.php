<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use common\base\enum\MobilePrefix;
use common\base\validators\MobileNumberValidator;
use yii\base\BaseObject;
use yii\base\InvalidCallException;
use Yii;

/**
 * Class MobilePhoneHelper
 * @package icares\helpers
 */
class MobilePhoneHelper extends BaseObject
{
    /**
     * @var string
     */
    public $cleanValue;
    /**
     * @var array
     */
    public $country;
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var string
     */
    public $number;
    /**
     * @var string
     */
    public $reason;
    /**
     * @var bool
     */
    public $isValid = false;

    /**
     * @var string
     */
    private $_value;

    /**
     * init check
     */
    public function init()
    {
        parent::init();

        if (!isset($this->_value)) {
            throw new InvalidCallException('$value must be provided !');
        }

        $this->resolveValue();
    }

    /**
     * resolve the value
     * @return bool
     */
    public function resolveValue()
    {
        $this->cleanValue = preg_replace('/[^0-9\+]/', '', $this->getValue());
        $this->cleanValue = trim($this->cleanValue);

        $length = strlen($this->cleanValue);
        $first = substr($this->cleanValue, 0, 1);
        $firstTwo = substr($this->cleanValue, 0, 2);

        if (($pos = strpos($this->cleanValue, '+')) !== false && $pos >= 1) {
            $this->reason = Yii::t('helper', 'Invalid Phone Number or Not Supported !');
            return false;
        }

        if ($first === '+') {
            $prefixMatch = false;
            foreach (MobilePrefix::values() as $x) {
                $prefix = substr($this->cleanValue, 0, strlen($x));
                if ($x === $prefix) {
                    $prefixMatch = true;
                    break;
                }
            }

            //not in prefix list, not supported
            if (!isset($prefix) || $prefixMatch === false) {
                $this->reason = Yii::t('helper', 'Country not supported !');
                return false;
            }

            $number = substr($this->cleanValue, strlen($prefix));
            $number = ltrim($number, '0');

            $this->prefix = $prefix;
            $this->number = $number;
        } elseif ($first === '1' && $length >= 9 && $length <= 10) {
            $this->prefix = MobilePrefix::MALAYSIA;
            $this->number = $this->cleanValue;
        } elseif ($first === '0' && $length >= 9 && $length <= 11) {
            //malaysian landline numbers have 9-10 digits
            $number = ltrim($this->cleanValue, '0');
            $this->prefix = MobilePrefix::MALAYSIA;
            $this->number = $number;
        } elseif (($firstTwo === '60') && $length >= 11 && $length <= 12) {
            $number = substr($this->cleanValue, 2);
            $number = ltrim($number, '0');
            $this->prefix = MobilePrefix::MALAYSIA;
            $this->number = $number;
        } elseif (($first === '8' || $first === '9') && $length >= 8 && $length <= 8) {
            $this->prefix = MobilePrefix::SINGAPORE;
            $this->number = $this->cleanValue;
        } elseif (($firstTwo === '65') && $length >= 10 && $length <= 10) {
            $number = substr($this->cleanValue, 2);
            $number = ltrim($number, '0');
            $this->prefix = MobilePrefix::SINGAPORE;
            $this->number = $number;
        } elseif (($firstTwo == '86') && $length === 13) {
            $this->prefix = MobilePrefix::CHINA;
            $this->number = substr($this->cleanValue, 2);
        } elseif ($first === '1' && $length === 11) {
            $this->prefix = MobilePrefix::CHINA;
            $this->number = $this->cleanValue;
        }

        if (!isset($this->prefix) || !isset($this->number)) {
            $this->reason = Yii::t('helper', 'Invalid Phone Number or Not Supported !');
            return false;
        }

        $validator = new MobileNumberValidator(['prefixValue' => $this->prefix]);
        $this->isValid = $validator->validate($this->number, $this->reason);

        $maps = MobilePrefix::countryMaps();
        $this->country = ArrayHelper::getValue($maps, $this->prefix);
    }

    /**
     * @return string
     */
    public function getComplete()
    {
        return $this->prefix . $this->number;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'cleanValue' => $this->cleanValue,
            'country' => $this->country,
            'mobile' => [
                'prefix' => $this->prefix,
                'number' => $this->number,
                'complete' => $this->getComplete(),
            ],
            'attribute' => [
                'isValid' => $this->isValid
            ],
            'reason' => $this->reason,
        ];
    }

    /**
     * @param string $value
     */
    protected function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->_value;
    }

    /**
     * @param $value
     * @return array
     */
    public static function resolve($value)
    {
        $mobile = new static(['value' => $value]);
        return $mobile->toArray();
    }
}
