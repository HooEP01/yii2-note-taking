<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use common\base\helpers\UuidHelper;
use yii\validators\FilterValidator;
use Yii;

/**
 * Class ShortUuidValidator
 * @package common\base\validators
 */
class ShortUuidValidator extends FilterValidator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filter = function ($value) {
            if (is_string($value)) {
                return UuidHelper::decodeShort($value);
            }

            return $value;
        };

        parent::init();
    }
}
