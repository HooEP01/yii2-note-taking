<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class TokenBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class TokenBehavior extends Behavior
{
    /**
     * @var string
     */
    public $tokenAttribute = 'token';
    /**
     * @var int
     */
    public $tokenLength = 128;
    /**
     * @var bool
     */
    public $alwaysGenerate = false;

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'generateRandomTokenValue',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'generateRandomTokenValue',
        ];
    }

    /**
     * generate token before save
     */
    public function generateRandomTokenValue()
    {
        if ($this->owner->hasAttribute($this->tokenAttribute)) {
            $value = $this->owner->getAttribute($this->tokenAttribute);
            $value = trim($value);
            if (empty($value) || $this->alwaysGenerate) {
                $token = Yii::$app->security->generateRandomString($this->tokenLength);
                $this->owner->setAttribute($this->tokenAttribute, $token);
            }
        }

        return true;
    }
}
