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
 * Class DateTimeHelper
 * @package api\base\helpers
 */
class DateTimeHelper extends BaseObject
{
    /**
     * @param $value
     * @return array
     */
    public static function normalize($value)
    {
        if ($value instanceof \DateTime) {
            $datetime = new DateTime($value->format('Y-m-d H:i:s'));
        } else {
            $datetime = new DateTime($value);
        }

        return [
            'value' => $datetime->formatToDatabaseDatetime(),
            'timestamp' => $datetime->getTimestamp(),
            'text' => $datetime->format('d/m/Y g:ia'),
            'relative' => Yii::$app->formatter->asRelativeTime($datetime),
        ];
    }
}
