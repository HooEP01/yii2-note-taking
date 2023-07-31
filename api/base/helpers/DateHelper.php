<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\helpers;

use common\base\DateTime;
use yii\base\BaseObject;
use Yii;

/**
 * Class DateHelper
 * @package api\base\helpers
 */
class DateHelper extends BaseObject
{
    /**
     * @param $value
     * @return array
     */
    public static function normalize($value)
    {
        if ($value instanceof \DateTime) {
            $datetime = new DateTime($value->format('Y-m-d'));
        } else {
            $datetime = new DateTime($value);
        }

        return [
            'value' => $datetime->formatToDatabaseDate(),
            'timestamp' => $datetime->getTimestamp(),
            'text' => $datetime->format('d/m/Y'),
            'relative' => Yii::$app->formatter->asRelativeTime($datetime),
        ];
    }
}
