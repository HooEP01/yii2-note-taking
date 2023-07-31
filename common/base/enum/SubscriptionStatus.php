<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use yii\helpers\Html;
use Yii;

/**
 * Class SubscriptionStatus
 * @package app\base\enum
 */
class SubscriptionStatus extends BaseEnum
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::DRAFT => Yii::t('enum', 'subscription.status.draft'),
            self::PUBLISHED => Yii::t('enum', 'subscription.status.published'),
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
            case self::PUBLISHED :
                $classes[] = 'badge-success';
                break;
            default :
                $classes[] = 'badge-default';
                break;
        }

        Html::addCssClass($options, $classes);
        return Html::tag('span', self::resolve($value), $options);
    }
}
