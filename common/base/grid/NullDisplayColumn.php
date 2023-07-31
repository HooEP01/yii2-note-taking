<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

/**
 * Class NullDisplayColumn
 * @package common\base\grid
 */
class NullDisplayColumn extends DataColumn
{
    public $format = 'raw';

    /**
     * @param \yii\db\ActiveRecord $model
     * @param mixed                $key
     * @param int                  $index
     * @return string
     * @throws \Exception
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);

        if (empty($value)) {
            return \Yii::$app->formatter->nullDisplay;
        }

        return $value;
    }
}