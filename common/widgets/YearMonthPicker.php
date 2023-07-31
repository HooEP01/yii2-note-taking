<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

/**
 * Class YearMonthPicker
 * @package common\widgets
 */
class YearMonthPicker extends \kartik\date\DatePicker
{
    public $pluginOptions = [
        'format' => 'MM, yyyy',
        'maxViewMode' => 'years',
        'minViewMode' => 'months',
        'autoclose' => true,
    ];
}