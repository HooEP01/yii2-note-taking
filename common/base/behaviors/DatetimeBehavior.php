<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;


use common\base\DateTime;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class DatetimeBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class DatetimeBehavior extends Behavior
{
    /**
     * @var array
     */
    public $datetimeAttributes = [];
    /**
     * @var array
     */
    public $startDateAttributes = [];
    public $endDateAttributes = [];

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
     * @throws \Exception
     */
    public function beforeSave()
    {
        if (!empty($this->datetimeAttributes)) {
            foreach ($this->owner->getDirtyAttributes($this->datetimeAttributes) as $attribute => $value) {
                if (!empty($value)) {
                    $this->owner->{$attribute} = (new DateTime($value))->local()->formatToISO8601();
                }
            }
        }

        if (!empty($this->startDateAttributes)) {
            foreach ($this->owner->getDirtyAttributes($this->startDateAttributes) as $attribute => $value) {
                if (!empty($value)) {
                    $this->owner->{$attribute} = (new DateTime($value))->setTime(0, 0, 0)->local()->formatToISO8601();
                }
            }
        }

        if (!empty($this->endDateAttributes)) {
            foreach ($this->owner->getDirtyAttributes($this->endDateAttributes) as $attribute => $value) {
                if (!empty($value)) {
                    $this->owner->{$attribute} = (new DateTime($value))->setTime(23, 59, 59)->local()->formatToISO8601();
                }
            }
        }
    }
}