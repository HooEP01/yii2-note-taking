<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use yii\helpers\Html;
use Yii;

/**
 * Class LeadStatus
 * @package app\base\enum
 */
class LeadStatus extends BaseEnum
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const CONVERTED = 'converted';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::PENDING => Yii::t('enum', 'lead.status.pending'),
            self::APPROVED => Yii::t('enum', 'lead.status.approved'),
            self::REJECTED => Yii::t('enum', 'lead.status.rejected'),
            self::CONVERTED => Yii::t('enum', 'lead.status.converted'),
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
            case self::PENDING :
                $classes[] = 'badge-secondary';
                break;
            case self::APPROVED :
                $classes[] = 'badge-success';
                break;
            case self::REJECTED :
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
