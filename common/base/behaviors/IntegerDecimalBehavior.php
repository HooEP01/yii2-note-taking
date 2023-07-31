<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\helpers\ArrayHelper;
use common\base\helpers\IntegerDecimal;
use common\base\traits\RuntimeCache;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;

/**
 * Class IntegerDecimalBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class IntegerDecimalBehavior extends Behavior
{
    use RuntimeCache;

    /**
     * the attributes map e.g.
     * [
     *    'amount' => ['attribute' => 'amountValue', 'precision' => 'charge_precision'], //advance
     *    'percent' => 'percentValue', // attribute mapping, precision default to "precision"
     *    'total', // auto assume attribute = "totalValue"
     *    'total_amount', // auto assume attribute = "totalAmountValue"
     * ]
     * @var array
     */
    public $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
     * assign the decimal float value
     */
    public function afterFind()
    {
        $owner = $this->owner;
        foreach ($this->getIntegerDecimalAttributes() as $attribute => $map) {
            $valueAttribute = ArrayHelper::getValue($map, 'attribute');
            $precisionAttribute = ArrayHelper::getValue($map, 'precision');

            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($valueAttribute, true, false)
                && $owner->hasProperty($precisionAttribute, true, false)) {
                $decimal = IntegerDecimal::factoryFromInteger($owner->{$attribute}, $owner->{$precisionAttribute});
                $owner->{$valueAttribute} = $decimal->getFloatValue();
            }
        }
    }

    /**
     * put back the decimal float value back to integer
     */
    public function beforeSave()
    {
        $owner = $this->owner;
        foreach ($this->getIntegerDecimalAttributes() as $attribute => $map) {
            $valueAttribute = ArrayHelper::getValue($map, 'attribute');
            $precisionAttribute = ArrayHelper::getValue($map, 'precision');

            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($valueAttribute, true, false)
                && $owner->hasProperty($precisionAttribute, true, false)) {
                $value = $owner->{$valueAttribute};
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    $owner->{$attribute} = 0;
                } else {
                    $decimal = IntegerDecimal::factoryFromFloat($value, $owner->{$precisionAttribute});
                    $owner->{$attribute} = $decimal->getIntegerValue();
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getIntegerDecimalAttributes()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $items = [];
            foreach ($this->attributes as $key => $value) {
                //-- just the attribute name, the $value = attribute name
                if (is_int($key) && is_string($value)) {
                    if ($this->owner->hasAttribute($value)) {
                        $items[$value] = [
                            'attribute' => Inflector::variablize(sprintf('%s_value', $value)),
                            'precision' => 'precision',
                        ];
                    }
                } elseif (is_array($value)) {
                    $defaultAttribute = Inflector::variablize(sprintf('%s_value', $key));
                    $items[$key] = [
                        'attribute' => ArrayHelper::getValue($value, 'attribute', $defaultAttribute),
                        'precision' => ArrayHelper::getValue($value, 'precision', 'precision'),
                    ];
                } elseif (is_string($value)) {
                    $items[$key] = [
                        'attribute' => $value,
                        'precision' => 'precision',
                    ];
                }
            }

            return $items;
        });
    }


}