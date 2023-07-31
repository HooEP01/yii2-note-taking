<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;

/**
 * Class UuidHelper
 * @property $alphabets
 * @package common\base\helpers
 */
class UuidHelper extends BaseObject
{
    const GUEST_CODE = '-GUEST-';

    /**
     * @var array
     */
    private $_alphabets = [];

    /**
     * @var int
     */
    private $_alphabetsLength = 0;

    public function init()
    {
        parent::init();
        if (empty($this->_alphabets)) {
            $this->setAlphabets([
                '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
                'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n',
                'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            ]);
        }
    }

    /**
     * Encodes the given UUID to a shorter version.
     * For example:
     * - 4e52c919-513e-4562-9248-7dd612c6c1ca becomes fpfyRTmt6XeE9ehEKZ5LwF
     * - 59a3e9ab-6b99-4936-928a-d8b465dd41e0 becomes BnxtX5wGumMUWXmnbey6xH
     *
     * @param UuidInterface $uuid
     *
     * @return string
     * @throws MathException
     */
    public function encode(UuidInterface $uuid) : string
    {
        $number = BigInteger::of((string) $uuid->getInteger());
        if ($number->isZero()) {
            return static::GUEST_CODE;
        }

        return $this->numToString($number);
    }

    /**
     * Decodes the given short UUID to the original version.
     * For example:
     * - fpfyRTmt6XeE9ehEKZ5LwF becomes 4e52c919-513e-4562-9248-7dd612c6c1ca
     * - BnxtX5wGumMUWXmnbey6xH becomes 59a3e9ab-6b99-4936-928a-d8b465dd41e0
     *
     * @param string $shortUuid
     * @return UuidInterface
     */
    public function decode(string $shortUuid) : UuidInterface
    {
        return Uuid::fromInteger($this->stringToNum($shortUuid));
    }


    /**
     * Transforms a given (big) number to a string value, based on the set alphabet.
     *
     * @param BigInteger $number
     * @return string
     * @throws MathException
     */
    private function numToString(BigInteger $number) : string
    {
        $output = '';
        while ($number->isPositive()) {
            $digit = $number->mod($this->_alphabetsLength);
            $number = $number->dividedBy($this->_alphabetsLength, RoundingMode::DOWN);

            $output .= $this->_alphabets[(int) $digit->toInt()];
        }

        return $output;
    }

    /**
     * Transforms a given string to a (big) number, based on the set alphabet.
     *
     * @param string $string
     * @return BigInteger
     */
    private function stringToNum(string $string) : BigInteger
    {
        if ($string === static::GUEST_CODE) {
            return BigInteger::zero();
        }

        $number = BigInteger::of(0);
        foreach (str_split(strrev($string)) as $char) {
            $number = $number->multipliedBy($this->_alphabetsLength)->plus(array_search($char, $this->_alphabets, false));
        }

        return $number;
    }

    /**
     * @param array|string $value
     * @throws Exception
     */
    protected function setAlphabets($value)
    {
        if (is_string($value)) {
            $value = str_split($value);
        }

        if (!is_array($value)) {
            throw new Exception('Invalid Alphabets');
        }

        $unique = array_unique($value);
        if (count($unique) !== count($value)) {
            throw new Exception('Cannot have duplicate characters !!');
        }

        $this->_alphabets = $value;
        $this->_alphabetsLength = count($value);
    }

    /**
     * Returns the currently used alphabet for encoding and decoding.
     * @return array
     */
    public function getAlphabets() : array
    {
        return $this->_alphabets;
    }

    /**
     * @param string|null $uuid
     * @return string
     */
    public static function encodeShort(?string $uuid): string
    {
        if (empty($uuid)) {
            return '-empty-';
        }

        if (!static::isValid($uuid)) {
            return $uuid;
        }

        $helper = new static();
        return $helper->encode(Uuid::fromString($uuid));
    }

    /**
     * @param string|null $shortUuid
     * @return string|null
     */
    public static function decodeShort(?string $shortUuid): ?string
    {
        if (empty($shortUuid)) {
            return '-';
        }

        try {
            $helper = new static();
            return $helper->decode($shortUuid)->toString();
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return '-';
    }

    /**
     * Generate a UUID string (version 4 by default).
     *
     * @return string canonical format UUID, i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a
     */
    public static function uuid()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isValid($value)
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
        return (bool) preg_match($pattern, $value);
    }
}