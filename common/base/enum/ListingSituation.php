<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use yii\helpers\Html;
use Yii;

/**
 * Class ListingSituation
 * @package app\base\enum
 */
class ListingSituation extends BaseEnum
{
    const DRAFT = 'draft';
    const ACTIVE  = 'active';
    const INACTIVE  = 'inactive';
    const EXPIRED  = 'expired';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::DRAFT => Yii::t('enum', 'listing.situation.draft'),
            self::ACTIVE => Yii::t('enum', 'listing.situation.active'),
            self::INACTIVE => Yii::t('enum', 'listing.situation.inactive'),
            self::EXPIRED => Yii::t('enum', 'listing.situation.expired'),
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
            case self::DRAFT :
                $classes[] = 'badge-secondary';
                break;
            case self::ACTIVE :
                $classes[] = 'badge-success';
                break;
            case self::INACTIVE :
                $classes[] = 'badge-warning';
                break;
            case self::EXPIRED :
                $classes[] = 'badge-danger';
                break;
            default :
                $classes[] = 'badge-default';
                break;
        }

        Html::addCssClass($options, $classes);
        return Html::tag('span', self::resolve($value), $options);
    }
}
