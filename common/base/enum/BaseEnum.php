<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use yii\helpers\ArrayHelper;

/**
 * Class BaseEnum
 * @package common\base\enum
 */
abstract class BaseEnum
{
    /**
     * @return array
     */
    public static function options()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function descriptiveOptions()
    {
        return static::options();
    }

    /**
     * @return array
     */
    public static function values()
    {
        $options = static::options();
        return array_keys($options);
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function toArray()
    {
        $options = static::options();
        $descriptiveOptions = static::options();

        $items = [];
        foreach ($options as $value => $name) {
            $items[] = [
                'value' => $value,
                'name' => $name,
                'description' => ArrayHelper::getValue($descriptiveOptions, $value, $name),
            ];
        }

        return $items;
    }

    /**
     * @param string $key
     * @param bool $descriptive
     * @return string
     * @throws Exception
     */
    public static function resolve($key, $descriptive = false)
    {
        $options = $descriptive ? static::descriptiveOptions() : static::options();
        return ArrayHelper::getValue($options, $key, 'N/A');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, static::values());
    }
}