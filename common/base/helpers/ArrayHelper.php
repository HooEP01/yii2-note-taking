<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

/**
 * Class ArrayHelper
 * @package common\base\helpers
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Drop the selected keys and return the remaining array
     * @param array $array
     * @param array $keys
     * @return mixed
     */
    public static function dropKeys(&$array, $keys = [])
    {
        foreach ((array) $keys as $key) {
            if (is_string($key)) {
                ArrayHelper::remove($array, $key);
            }
        }

        return $array;
    }
}
