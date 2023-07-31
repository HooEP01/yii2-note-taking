<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;


use kartik\select2\Select2;
use yii\web\JsExpression;

class TagsInput extends Select2
{
    public $showToggleAll = false;
    public $data = [];

    /**
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->options = [
            'multiple' => true,
        ];

        $this->defaultOptions = ['class' => 'select-tags'];


        if (empty($this->pluginOptions)) {
            $this->pluginOptions = [
                'tags' => true,
                'tokenSeparators' => [','],
            ];
        }
        parent::init();
    }
}