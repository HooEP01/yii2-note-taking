<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\cli;

use common\jobs\Task;
use yii\queue\ExecEvent;

/**
 * Class Verbose
 * @package common\base\queue\core
 */
class VerboseBehavior extends \yii\queue\cli\VerboseBehavior
{
    /**
     * @param ExecEvent $event
     * @return string
     */
    protected function jobTitle(ExecEvent $event)
    {
        if ($event->job instanceof Task) {
            $message = $event->job->getVerboseMessage();
            $extra = "attempt: $event->attempt";
            if ($pid = $event->sender->getWorkerPid()) {
                $extra .= ", pid: $pid";
            }
            return " [$event->id] $message ($extra)";
        }

        return parent::jobTitle($event);
    }
}
