<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use yii\validators\FilterValidator;
use Yii;

/**
 * Class StripCleanValidator
 * @package common\base\validators
 */
class StripCleanValidator extends FilterValidator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filter = function ($value) {
            if (is_string($value)) {
                return Yii::$app->sanitizer->stripClean($value);
            }

            return $value;
        };

        parent::init();
    }
}
