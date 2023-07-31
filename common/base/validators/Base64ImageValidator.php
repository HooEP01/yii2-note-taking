<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use yii\validators\Validator;
use Yii;

/**
 * Class Base64ImageValidator
 * @package common\base\validators
 */
class Base64ImageValidator extends Validator
{
    /**
     * @var array|string a list of image formats that are allowed.
     * This can be either an array or a string consisting of file formats
     * separated by space or comma (e.g. "gif, jpg, png").
     * Format names are case-insensitive. Defaults to null, meaning all supported image formats extensions are allowed.
     */
    public $formats;

    /**
     * @var array
     */
    private $_mimeTypes = [
        'jpeg' => 'FFD8',
        'jpg' => 'FFD8',
        'png' => '89504E470D0A1A0A',
        'gif' => '474946',
        'bmp' => '424D',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('validator', '{attribute} is not a valid Image Base64 encoded string.');
        }

        if (!is_array($this->formats)) {
            $this->formats = preg_split('/[\s,]+/', strtolower($this->formats), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->formats = array_map('strtolower', $this->formats);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $valid = false;

        $data = base64_decode($value);

        $mimeTypes = $this->_mimeTypes;
        if (!empty($this->formats)) {
            $mimeTypes = array_filter($this->_mimeTypes, function ($key) {
                return in_array($key, $this->formats);
            }, ARRAY_FILTER_USE_KEY);
        }

        foreach ($mimeTypes as $mime => $hex) {
            $bytes = $this->getBytesFromHexString($hex);
            if (substr($data, 0, strlen($bytes)) == $bytes) {
                $valid = true;
                break;
            }
        }

        return $valid ? null : [$this->message, []];
    }


    /**
     * @param string $hex
     * @return string
     */
    protected function getBytesFromHexString($hex)
    {
        $bytes = [];
        for ($count = 0; $count < strlen($hex); $count += 2) {
            $bytes[] = chr(hexdec(substr($hex, $count, 2)));
        }

        return implode($bytes);
    }
}
