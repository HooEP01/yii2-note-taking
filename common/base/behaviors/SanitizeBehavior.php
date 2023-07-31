<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\services\Sanitizer;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\di\Instance;

/**
 * Class SanitizeBehavior
 * @package backend\base\behaviors
 */
class SanitizeBehavior extends Behavior
{
    /**
     * @var array
     */
    public $purifyAttributes = [];
    /**
     * @var array
     */
    public $stripCleanAttributes = [];

    /**
     * @var string|array|Sanitizer
     */
    public $sanitizer = 'sanitizer';

    /**
     * ensure sanitizer
     */
    public function init()
    {
        parent::init();

        $this->sanitizer = Instance::ensure($this->sanitizer, Sanitizer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'sanitize',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'sanitize',
        ];
    }

    /**
     * purify or strip clean value
     */
    public function sanitize()
    {
        foreach ($this->purifyAttributes as $attribute) {
            $this->owner->{$attribute} = $this->sanitizer->purify($this->owner->{$attribute});
        }

        foreach ($this->stripCleanAttributes as $attribute) {
            $this->owner->{$attribute} = $this->sanitizer->stripClean($this->owner->{$attribute});
        }
    }
}
