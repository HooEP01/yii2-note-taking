<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

/**
 * Class Json
 * @package common\base\helpers
 */
class Json extends \yii\helpers\Json
{
    /**
     * Validate is the given string is JSON
     * @param string|mixed $content
     * @return bool
     */
    public static function validate($content)
    {
        if (!is_string($content) || empty($content)) {
            return false;
        }

        $firstChar = substr($content, 0, 1);
        $lastChar = substr($content, -1, 1);

        return ($firstChar === '{' && $lastChar === '}')
            || ($firstChar === '[' && $lastChar === ']');
    }
}
