<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class BooleanColumn
 * @package common\base\grid
 */
class BooleanColumn extends DataColumn
{
    /**
     * @var string
     */
    public $format = 'raw';

    /**
     * @var array
     */
    public $headerOptions = ['style' => 'width: 75px; text-align: center;'];

    /**
     * @var array
     */
    public $contentOptions = ['class' => 'text-center'];

    /**
     * Returns the data cell value.
     * @param ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = (bool) parent::getDataCellValue($model, $key, $index);
        if ($value === false) {
            return Html::tag('label', $this->grid->formatter->asBoolean($value), ['class' => 'badge badge-danger']);
        }

        return Html::tag('label', $this->grid->formatter->asBoolean($value), ['class' => 'badge badge-success']);
    }

    /**
     * @return array|false|string|null
     */
    protected function renderFilterCellContent()
    {
        if (isset($this->filter)) {
            return parent::renderFilterCellContent();
        }

        $model = $this->grid->filterModel;
        if ($model->hasErrors($this->attribute)) {
            Html::addCssClass($this->filterOptions, 'has-error');
            $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
        } else {
            $error = '';
        }

        $options = array_merge(['prompt' => ''], $this->filterInputOptions);
        return Html::activeDropDownList($model, $this->attribute, [
                1 => $this->grid->formatter->booleanFormat[1],
                0 => $this->grid->formatter->booleanFormat[0],
            ], $options) . $error;
    }
}
