<?php
/**
 * @author RYU Chua <me@ryu.my>
 */

namespace common\base\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * class NullOnEmptyBehavior
 */
class NullOnEmptyBehavior extends Behavior
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'setEmptyToNull',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'setEmptyToNull',
        ];
    }

    /**
     * set value to empty
     */
    public function setEmptyToNull()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->{$attribute};
            if ($value === '') {
                $this->owner->{$attribute} = null;
            }
        }
    }
}