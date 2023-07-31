<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class ActionType
 * @package app\base\enum
 */
class ActionType extends BaseEnum
{
    const VIEW = 'view';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::VIEW => 'view',
        ];
    }
}
