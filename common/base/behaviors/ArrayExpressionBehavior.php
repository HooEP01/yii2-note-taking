<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;


use common\base\helpers\ArrayHelper;
use common\base\helpers\StringHelper;
use common\base\helpers\UuidHelper;
use common\base\traits\RuntimeCache;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\ArrayExpression;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;
use Yii;

/**
 * Class ArrayExpressionBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class ArrayExpressionBehavior extends Behavior
{
    const TYPE_UUID = 'uuid';
    const TYPE_TEXT = 'text';

    use RuntimeCache;

    /**
     * the attributes map e.g.
     * [
     *    'category_items', // auto assume items attribute = "categoryItems"
     *    'category_items' => 'categoryItems'
     *    'category_items' => ['attribute' => 'categoryItems', 'type' => 'uuid']
     * ]
     *
     * @var array
     */
    public $attributes = [];
    public $defaultType = 'text';

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
     * convert array expression to array
     */
    public function afterFind()
    {
        $owner = $this->owner;
        foreach ($this->getArrayExpressionAttributes() as $attribute => $map) {
            $itemsAttribute = ArrayHelper::getValue($map, ['attribute']);
            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($itemsAttribute, true, false)) {

                $attributeValue = $owner->{$attribute};
                if ($attributeValue instanceof ArrayExpression) {
                    $owner->{$itemsAttribute} = $attributeValue->getValue();
                }
            }
        }
    }

    /**
     * convert array to array expression
     */
    public function beforeSave()
    {
        $owner = $this->owner;
        foreach ($this->getArrayExpressionAttributes() as $attribute => $map) {
            $itemsAttribute = ArrayHelper::getValue($map, ['attribute']);
            $type = ArrayHelper::getValue($map, ['type'], $this->defaultType);

            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($itemsAttribute, true, false)) {

                if ($type === static::TYPE_UUID) {
                    $itemsAttributeValue = [];
                    foreach ($owner->{$itemsAttribute} as $item) {
                        if (UuidHelper::isValid($item)) {
                            $itemsAttributeValue[] = $item;
                        } else {
                            $uuid = UuidHelper::decodeShort($item);
                            if (UuidHelper::isValid($uuid)) {
                                $itemsAttributeValue[] = $uuid;
                            }
                        }
                    }
                } else {
                    $itemsAttributeValue = [];
                    foreach ($owner->{$itemsAttribute} as $item) {
                        $value = Yii::$app->sanitizer->stripClean((string) $item);
                        if (!empty($value)) {
                            $itemsAttributeValue[] = $value;
                        }
                    }
                }

                if (empty($itemsAttributeValue)) {
                    $attributeValue = null;
                } else {
                    $attributeValue = new ArrayExpression($itemsAttributeValue, $type);
                }

                $owner->{$attribute} = $attributeValue;
            }
        }
    }

    /**
     * convert string to array
     */
    public function beforeValidate()
    {
        $owner = $this->owner;
        foreach ($this->getArrayExpressionAttributes() as $attribute => $map) {
            $itemsAttribute = ArrayHelper::getValue($map, ['attribute']);

            if ($owner->hasProperty($attribute, true, false)
                && $owner->hasProperty($itemsAttribute, true, false)) {
                $itemsAttributeValue = $owner->{$itemsAttribute};

                if (is_string($itemsAttributeValue)) {
                    $owner->{$itemsAttribute} = StringHelper::explodeByComma($itemsAttributeValue);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayExpressionAttributes()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $items = [];
            foreach ($this->attributes as $key => $value) {
                //attribute name
                if (is_int($key) && is_string($value)) {
                    if ($this->owner->hasAttribute($value)) {
                        $items[$value] = ['attribute' => Inflector::variablize($value)];
                    }
                } elseif (is_array($value)) {
                    $defaultAttribute = Inflector::variablize(sprintf('%s_value', $key));
                    $items[$key] = [
                        'attribute' => ArrayHelper::getValue($value, 'attribute', $defaultAttribute),
                        'type' => ArrayHelper::getValue($value, 'type', $this->defaultType),
                    ];
                } elseif (is_string($value)) {
                    $items[$key] = ['attribute' => $value];
                }
            }

            return $items;
        });
    }
}