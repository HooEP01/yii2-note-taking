<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;


use common\base\enum\RateType;
use Yii;

class RateHelper
{

    /**
     * Returns Integer Value
     * @param      $base
     * @param      $type
     * @param      $value
     * @param      $precision
     * @return int
     * @throws \yii\base\NotSupportedException
     */
    public static function calculate($base, $type, $value, $precision)
    {
        if ($type === RateType::NONE) {
            return 0;
        }

        if ($type === RateType::PERCENT && $value > 0) {
            if (is_scalar($base)) {
                $base = IntegerDecimal::factoryFromInteger($base);
            }

            return $base
                ->multiply($value)
                ->divide(100 * 100)
                ->setPrecision($precision)
                ->getIntegerValue();
        }

        return $value;
    }

    /**
     * @param $type
     * @param $value
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function description($type, $value, $options = [])
    {
        if ($value instanceof IntegerDecimal) {
            $value = $value->getIntegerValue();
        }

        $precision = ArrayHelper::getValue($options, 'precision', 2);
        switch ($type) {
            case RateType::PERCENT:
                return Yii::$app->formatter->asPercentage($value, $precision);
                break;
            case RateType::AMOUNT:
                return Yii::$app->formatter->asAccountingPrice($value, $options);
                break;
            case RateType::NONE:
                return Yii::$app->formatter->asAccountingPrice(0, $options);
                break;
            default:
                return Yii::$app->formatter->asAccountingAmount($value, $options);
                break;
        }
    }
}