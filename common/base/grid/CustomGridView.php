<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use yii\helpers\Html;

/**
 * Class CustomGridView
 * @package common\base\grid
 */
class CustomGridView extends GridView
{
    public $itemView;

    public $viewParams = [];

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderTableRow($model, $key, $index)
    {
        if (!isset($this->itemView)) {
            return parent::renderTableRow($model, $key, $index);
        }

        $colspan = count($this->columns);

        if ($this->rowOptions instanceof \Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }

        $data = ['model' => $model, 'index' => $index, 'key' => $key];
        $data = array_merge($data, $this->viewParams);

        $cell = Html::tag('td', $this->render($this->itemView, $data), ['colspan' => $colspan]);

        return Html::tag('tr', $cell, $options);
    }
}