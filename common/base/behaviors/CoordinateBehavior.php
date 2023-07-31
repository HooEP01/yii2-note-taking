<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\helpers\StringHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class CoordinateBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class CoordinateBehavior extends Behavior
{
    /**
     * @var string
     */
    public $coordinateAttribute = 'coordinate';
    /**
     * @var string
     */
    public $longitudeAttribute = 'longitude';
    /**
     * @var string
     */
    public $latitudeAttribute = 'latitude';
    /**
     * @var bool
     */
    public $defaultEmpty = false;

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeUpdate',
        ];
    }

    /**
     * update the coordinate
     */
    public function afterFind()
    {
        if (empty($this->owner->{$this->longitudeAttribute}) && !$this->defaultEmpty) {
            $this->owner->{$this->longitudeAttribute} = 103.72402060416755;
        }

        if (empty($this->owner->{$this->latitudeAttribute}) && !$this->defaultEmpty) {
            $this->owner->{$this->latitudeAttribute} = 1.5925056446750858;
        }

        if ($this->owner->canSetProperty($this->coordinateAttribute)) {
            $this->owner->{$this->coordinateAttribute} = sprintf('%s,%s', $this->owner->{$this->longitudeAttribute}, $this->owner->{$this->latitudeAttribute});
        }
    }

    /**
     * before update
     */
    public function beforeUpdate()
    {
        if ($this->owner->scenario === 'coordinate') {
            if (!empty($this->owner->{$this->coordinateAttribute})) {
                list($longitude, $latitude) = StringHelper::explodeByComma($this->owner->{$this->coordinateAttribute});
                if (isset($longitude) && isset($latitude)) {
                    $this->owner->setAttribute($this->longitudeAttribute, $longitude);
                    $this->owner->setAttribute($this->latitudeAttribute, $latitude);
                }
            }

            if (($coordinate = $this->owner->getAttribute($this->coordinateAttribute)) && !empty($coordinate)) {
                list($longitude, $latitude) = StringHelper::explodeByComma($coordinate);
                if (isset($longitude) && isset($latitude)) {
                    $this->owner->{$this->longitudeAttribute} = $longitude;
                    $this->owner->{$this->latitudeAttribute} = $latitude;
                }
            }
        }

        return true;
    }
}
