<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\helpers\UuidHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class UuidBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class UuidBehavior extends Behavior
{
    /**
     * @var string
     */
    public $uuidAttribute = 'id';


    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'generateUuidValue',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'generateUuidValue',
        ];
    }

    /**
     * generate uuid before save
     */
    public function generateUuidValue()
    {
        if ($this->owner->hasAttribute($this->uuidAttribute)) {
            $value = $this->owner->getAttribute($this->uuidAttribute);
            $value = trim($value);
            if (empty($value)) {
                $this->owner->setAttribute($this->uuidAttribute, UuidHelper::uuid());
            }
        }

        return true;
    }
}
