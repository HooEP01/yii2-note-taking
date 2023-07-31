<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

/**
 * Class DatePicker
 * @package common\widgets
 */
class DatePicker extends \kartik\date\DatePicker
{
    public $pluginOptions = [
        'format' => 'yyyy-mm-dd'
    ];
}