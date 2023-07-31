<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

/**
 * @author RYU Chua <me@ryu.my>
 */

namespace common\base\bootstrap4;

use common\base\behaviors\TranslationBehavior;

/**
 * Class ActiveField
 * @package common\base\bootstrap4
 */
class ActiveField extends \yii\bootstrap4\ActiveField
{
    /**
     * {@inheritdoc}
     */
    public function label($label = null, $options = [])
    {
        parent::label($label, $options);
        if (isset($this->parts['{label}'])) {
            /** @var TranslationBehavior $translation */
            $translation = $this->model->getBehavior('translation');
            if ($translation instanceof TranslationBehavior) {
                if (in_array($this->attribute, $translation->attributes)) {
                    $this->parts['{label}'] = $this->parts['{label}'] . ' <small class="text-danger">*[' . $translation->getLanguageName() . ']*</small>';
                }
            }
        }

        return $this;
    }
}