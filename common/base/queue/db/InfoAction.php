<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\base\queue\db;

use yii\db\Query;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\queue\cli\Action;

/**
 * Info about queue status.
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class InfoAction extends \yii\queue\db\InfoAction
{
    /**
     * @var Queue
     */
    public $queue;


    /**
     * Info about queue status.
     */
    public function run()
    {
        Console::output($this->format('Jobs', BaseConsole::FG_GREEN));

        Console::stdout($this->format('- waiting: ', BaseConsole::FG_YELLOW));
        Console::output($this->getWaiting()->count('*', $this->queue->db));

        Console::stdout($this->format('- delayed: ', BaseConsole::FG_YELLOW));
        Console::output($this->getDelayed()->count('*', $this->queue->db));

        Console::stdout($this->format('- reserved: ', BaseConsole::FG_YELLOW));
        Console::output($this->getReserved()->count('*', $this->queue->db));

        Console::stdout($this->format('- done: ', BaseConsole::FG_YELLOW));
        Console::output($this->getDone()->count('*', $this->queue->db));
    }

    /**
     * @return Query
     */
    protected function getWaiting()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere(['reservedAt' => null])
            ->andWhere(['delay' => 0]);
    }

    /**
     * @return Query
     */
    protected function getDelayed()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere(['reservedAt' => null])
            ->andWhere(['>', 'delay', 0]);
    }

    /**
     * @return Query
     */
    protected function getReserved()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere('[[reservedAt]] is not null')
            ->andWhere(['doneAt' => null]);
    }

    /**
     * @return Query
     */
    protected function getDone()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere('[[doneAt]] is not null');
    }
}
