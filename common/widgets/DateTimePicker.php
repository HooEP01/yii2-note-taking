<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

/**
 * Class DateTimePicker
 * @package common\widgets
 */
class DateTimePicker extends \kartik\datetime\DateTimePicker
{
    public $pluginOptions = [
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayHighlight' => true,
        'todayBtn' => true,
        'autoclose' => true,
    ];
}