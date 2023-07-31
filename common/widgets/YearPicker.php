<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

/**
 * Class DatePicker
 * @package common\widgets
 */
class YearPicker extends \kartik\date\DatePicker
{
    public $pluginOptions = [
        'format' => 'MM, yyyy',
        'maxViewMode' => 'years',
        'minViewMode' => 'months',
        'autoclose' => true,
    ];
}