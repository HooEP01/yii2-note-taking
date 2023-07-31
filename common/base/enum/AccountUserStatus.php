<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;
use yii\helpers\Html;

/**
 * Class AccountUserStatus
 * @package app\base\enum
 */
class AccountUserStatus extends BaseEnum
{
    const ACTIVE = 'active';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ACTIVE => Yii::t('enum', 'status.active'),
        ];
    }

    /**
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function generateLabel($value, $options = [])
    {
        $classes = ['badge'];
        switch ($value) {
            case self::ACTIVE :
                $classes[] = 'badge-secondary';
                break;
            default :
                $classes[] = 'badge-default';
                break;
        }

        Html::addCssClass($options, $classes);
        return Html::tag('span', self::resolve($value), $options);
    }
}
