<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use yii\helpers\Html;
use Yii;

/**
 * Class ListingStatus
 * @package app\base\enum
 */
class ListingStatus extends BaseEnum
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const PRIVATE  = 'private';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::DRAFT => Yii::t('enum', 'listing.status.draft'),
            self::PUBLISHED => Yii::t('enum', 'post.status.published'),
            self::PRIVATE => Yii::t('enum', 'post.status.private'),
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
            case self::PRIVATE :
                $classes[] = 'badge-warning';
                break;
            default :
                $classes[] = 'badge-default';
                break;
        }

        Html::addCssClass($options, $classes);
        return Html::tag('span', self::resolve($value), $options);
    }
}
