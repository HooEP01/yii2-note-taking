<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;


use common\base\DateTime;
use common\base\helpers\ArrayHelper;
use common\base\traits\RuntimeCache;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;

/**
 * Class DateFormatBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class DateFormatBehavior extends Behavior
{
    use RuntimeCache;

    /**
     * the attributes map e.g.
     * [
     *    'startAt', // auto assume items attribute = "startAtValue"
     * ]
     *
     * @var array
     */
    /**
     * the attributes map e.g.
     * [
     *    'startAt' => ['attribute' => 'startAtValue', 'format' => 'M, Y'], //advance
     *    'startAt' => 'startAtValue', // attribute mapping, format default to $defaultFormat
     *    'start_at', // auto assume attribute = "startAtValue"
     *    'startDatetime', // auto assume attribute = "startDatetimeValue"
     * ]
     * @var array
     */
    public $attributes = [];

    public $defaultFormat = 'Y-m-d';

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
     * assign the datetime into formatted value
     */
    public function afterFind()
    {
        $owner = $this->owner;
        foreach ($this->getDateFormatAttributes() as $attribute => $map) {
            $valueAttribute = ArrayHelper::getValue($map, 'attribute');
            $format = ArrayHelper::getValue($map, 'format', $this->defaultFormat);

            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($valueAttribute, true, false)) {
                if (!empty($owner->{$attribute})) {
                    $datetime = new DateTime($owner->{$attribute});
                    $owner->{$valueAttribute} = $datetime->format($format);
                }
            }
        }
    }

    /**
     * put back the formatted value back to iso date
     */
    public function beforeSave()
    {
        $owner = $this->owner;
        foreach ($this->getDateFormatAttributes() as $attribute => $map) {
            $valueAttribute = ArrayHelper::getValue($map, 'attribute');
            $format = ArrayHelper::getValue($map, 'format', $this->defaultFormat);
            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($valueAttribute, true, false)) {
                $value = $owner->{$valueAttribute};
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    $owner->{$attribute} = null;
                    continue;
                }

                $datetime = DateTime::createFromFormat($format, $value);
                $owner->{$attribute} = $datetime->format(DateTime::ATOM);
            }
        }
    }

    /**
     * @return array
     */
    protected function getDateFormatAttributes()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $items = [];
            foreach ($this->attributes as $key => $value) {
                //-- just the attribute name, the $value = attribute name
                if (is_int($key) && is_string($value)) {
                    if ($this->owner->hasAttribute($value)) {
                        $items[$value] = ['attribute' => Inflector::variablize(sprintf('%s_value', $value))];
                    }
                } elseif (is_array($value)) {
                    $defaultAttribute = Inflector::variablize(sprintf('%s_value', $key));
                    $items[$key] = [
                        'attribute' => ArrayHelper::getValue($value, 'attribute', $defaultAttribute),
                        'format' => ArrayHelper::getValue($value, 'format', $this->defaultFormat),
                    ];
                } elseif (is_string($value)) {
                    $items[$key] = ['attribute' => $value];
                }
            }

            return $items;
        });
    }
}