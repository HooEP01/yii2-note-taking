<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\sqs;

use Yii;

/**
 * Class Queue
 * @package common\base\queue\sqs
 */
class Queue extends \yii\queue\sqs\Queue
{

    /**
     * @var string command class name
     */
    public $commandClass = Command::class;
}