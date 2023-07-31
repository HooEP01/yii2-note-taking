<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use common\base\helpers\StringHelper;
use yii\validators\FilterValidator;
use Yii;

/**
 * Class CommaSeparateValidator
 * @package common\base\validators
 */
class CommaSeparateValidator extends FilterValidator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filter = function ($value) {
            if (is_string($value)) {
                return StringHelper::explodeByComma($value);
            }

            return $value;
        };

        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('validator', '{attribute} is not a valid comma separated value or array');
        }
    }
}
