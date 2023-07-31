<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;

/**
 * Class DatetimeColumn
 * @package common\base\grid
 */
class DatetimeColumn extends DataColumn
{
    /**
     * @var string
     */
    public $format = 'datetime';

    /**
     * @var array
     */
    public $headerOptions = ['style' => 'width: 150px; text-align: center;'];

    /**
     * @var array
     */
    public $contentOptions = ['class' => 'text-center'];

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

        $options = array_merge([], $this->filterInputOptions);

        return DateRangePicker::widget([
                'model' => $model,
                'attribute' => $this->attribute,
                'options' => $options,
                'convertFormat' => true,
                'pluginOptions' => [
                    'timePicker' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i'
                    ],
                    'opens' => 'left'
                ]
            ]) . $error;
    }
}
