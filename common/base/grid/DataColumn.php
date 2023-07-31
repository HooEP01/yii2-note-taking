<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class DataColumn
 * @package common\base\grid
 */
class DataColumn extends \yii\grid\DataColumn
{
    /**
     * @var array
     */
    public $filterInputOptions = ['class' => 'form-control', 'id' => null, 'prompt' => '--All--'];

    /**
     * Returns the data cell value.
     *
     * @param ActiveRecord $model the data model
     * @param mixed        $key   the key associated with the data model
     * @param int          $index the zero-based index of the data model among the models array returned by
     *                            [[GridView::dataProvider]].
     * @return string the data cell value
     * @throws \Exception
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->format === 'raw') {
            return parent::getDataCellValue($model, $key, $index);
        }

        //-- if filter is set, then use the values
        if (isset($this->filter) && is_array($this->filter)) {
            $value = ArrayHelper::getValue($model, $this->attribute);
            if (is_string($value)) {
                return isset($this->filter[$value]) ? $this->filter[$value] : $value;
            }
        }

        return parent::getDataCellValue($model, $key, $index);
    }
}