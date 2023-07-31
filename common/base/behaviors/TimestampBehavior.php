<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\DateTime;

/**
 * Class TimestampBehavior
 * @package common\base\behaviors
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public $createdAtAttribute = 'createdAt';
    public $updatedAtAttribute = 'updatedAt';

    /**
     * @inheritdoc
     * @return mixed
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            $now = new DateTime();
            return $now->formatToISO8601();
        }
        return parent::getValue($event);
    }
}
