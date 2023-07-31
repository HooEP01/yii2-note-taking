<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.alpstein.my
 * @copyright Copyright (c) Alpstein Technology Sdn Bhd
 */

namespace api\base\helpers;

use common\base\helpers\ArrayHelper;
use common\base\helpers\IntegerDecimal;
use yii\base\BaseObject;
use yii\base\InvalidCallException;
use Yii;

/**
 * Class IntegerPriceHelper
 * @package api\base\helpers
 */
class IntegerPriceHelper extends BaseObject
{
    /**
     * @param int|IntegerDecimal $amount
     * @param array $options
     * @return array
     */
    public static function normalize($amount, $options = [])
    {
        $value = $amount;
        if (is_int($amount)) {
            $precision = (int) ArrayHelper::getValue($options, 'precision', 2);
            $value = IntegerDecimal::factoryFromInteger($amount, $precision);
        }

        $suffix = ArrayHelper::getValue($options, 'suffix', '');
        if ($value instanceof IntegerDecimal) {
            $data = [
                'value' => $value->getFloatValue(),
                'text' => Yii::$app->formatter->asAccountingPrice($value, $options) . $suffix,
            ];
            if (YII_DEBUG) {
                $data['_debug'] = [
                    'actual' => $value->getIntegerValue(),
                    'precision' => $value->getPrecision(),
                ];
            }

            return $data;
        }

        throw new InvalidCallException('Invalid Helper Call !');
    }
}
