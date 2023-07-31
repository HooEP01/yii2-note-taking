<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;


use kartik\select2\Select2;

class SelectDropdown extends Select2
{
    /**
     * @var bool
     */
    public $multiple = false;
    public $placeholder;

    /**
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->options = [
            'placeholder' => $this->placeholder ?: \Yii::t('backend', 'form.dropdown.select_one'),
            'multiple' => $this->multiple,
        ];

        if (!isset($this->pluginOptions)) {
            $this->pluginOptions = [
                'allowClear' => true,
            ];
        }
        parent::init();
    }
}