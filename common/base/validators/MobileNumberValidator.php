<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use common\base\enum\MobilePrefix;
use yii\validators\Validator;
use Yii;

/**
 * Class MobileNumberValidator
 * @package common\base\validators
 */
class MobileNumberValidator extends Validator
{
    /**
     * @var string
     */
    public $prefixValue;

    /**
     * @var string
     */
    public $prefixAttribute = 'mobilePrefix';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('validator', '{attribute} is not a valid mobile number of the country');
        }
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        if (isset($this->prefixValue)) {
            return $this->prefixValue;
        }

        return $this->prefixValue = MobilePrefix::MALAYSIA;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->prefixValue === null) {
            if (!empty($model->{$this->prefixAttribute})) {
                $this->prefixValue = $model->{$this->prefixAttribute};
            } else {
                $this->prefixValue = MobilePrefix::MALAYSIA;
            }
        }

        //--- remove leading zeros
        $model->{$attribute} = ltrim($model->{$attribute}, '0');

        parent::validateAttribute($model, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $prefix = $this->getPrefix();
        switch ($prefix) {
            case MobilePrefix::MALAYSIA:
                $valid = $this->validateMalaysiaPhone($value);
                break;
            case MobilePrefix::SINGAPORE:
                $valid = $this->validateSingaporePhone($value);
                break;
            case MobilePrefix::CHINA:
                $valid = $this->validateChinaPhone($value);
                break;
            case MobilePrefix::TAIWAN:
                $valid = $this->validateTaiwanPhone($value);
                break;
            case MobilePrefix::HONG_KONG:
                $valid = $this->validateHongKongPhone($value);
                break;
            case MobilePrefix::JAPAN:
                $valid = $this->validateJapanPhone($value);
                break;
            case MobilePrefix::KOREA:
                $valid = $this->validateKoreaPhone($value);
                break;
            case MobilePrefix::INDONESIA:
                $valid = $this->validateIndonesiaPhone($value);
                break;
            case MobilePrefix::INDIA:
                $valid = $this->validateIndiaPhone($value);
                break;
            case MobilePrefix::NEW_ZEALAND:
                $valid = $this->validateNewZealandPhone($value);
                break;
            case MobilePrefix::USA_CANADA:
                $valid = $this->validateUsaCanadaPhone($value);
                break;
            default:
                $valid = true;
                break;
        }

        return $valid ? null : [$this->message, []];
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateMalaysiaPhone($value)
    {
        //-- first digit must be one
        if (substr($value, 0, 1) !== '1') {
            return false;
        }

        //-- third digit must be one
        if (substr($value, 2, 1) === '0') {
            return false;
        }

        //-- include operator, length must be between 9-10
        return ($length = strlen($value)) >= 9 && $length <= 10;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateSingaporePhone($value)
    {
        $firstDigit = substr($value, 0, 1);
        //-- first digit must be 8 / 9
        if ($firstDigit !== '8' && $firstDigit !== '9') {
            return false;
        }

        //-- length must be between 7-8
        return ($length = strlen($value)) >= 7 && $length <= 8;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateChinaPhone($value)
    {
        //https://github.com/VincentSit/ChinaMobilePhoneNumberRegex/blob/master/README-CN.md - All Mobile
        $regex = '/^(?:\+?86)?1(?:3\d{3}|5[^4\D]\d{2}|8\d{3}|7(?:[01356789]\d{2}|4(?:0\d|1[0-2]|9\d))|9[189]\d{2}|6[567]\d{2}|4(?:[14]0\d{3}|[68]\d{4}|[579]\d{2}))\d{6}$/';
        return (bool) preg_match($regex, $value);


        //By HV:
        /**
        $first = substr($value, 0, 1);
        $firstTwo = substr($value, 0, 2);
        if ($first !== '1') {
            return false;
        }

        if (!in_array($firstTwo, ['13', '15', '18'])) {
            return false;
        }

        return strlen($value) === 11;
         */
    }

    /**
     * @param $value
     * @return bool
     * @throws \Safe\Exceptions\PcreException
     */
    protected function validateTaiwanPhone($value)
    {
        $regex = '/^9\d{8}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateHongKongPhone($value)
    {
        $regex = '/^\d{8}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateJapanPhone($value)
    {
        $regex = '/^\d{10}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateKoreaPhone($value)
    {
        $regex = '/^1\d{5,11}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateIndonesiaPhone($value)
    {
        //first digit is always 8, followed by 8-10 digits (total 9-11 digits)
        $regex = '/^8\d{8,10}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateIndiaPhone($value)
    {
        //first digit ranges from 6-9, followed by 9 digits (always 10 digits)
        $regex ='/^[6-9]\d{9}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateNewZealandPhone($value)
    {
        //first digit is always 2, followed by 7 - 9 digits (total 8-10 digits)
        $regex = '/^2\d{7,9}$/';
        return (bool) preg_match($regex, $value);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function validateUsaCanadaPhone($value)
    {
        //10 digits
        $regex = '/\d{10}$/';
        return (bool) preg_match($regex, $value);
    }
}
