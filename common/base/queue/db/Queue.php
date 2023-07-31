<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\db;

use common\base\helpers\UuidHelper;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\Query;

/**
 * Class Queue
 * @package common\base\queue\db
 */
class Queue extends \yii\queue\db\Queue
{
    /**
     * @var string table name
     */
    public $tableName = '{{%system_queue}}';
    /**
 * @var string command class name
 */
    public $commandClass = Command::class;

    /**
     * @inheritdoc
     * @throws \yii\db\Exception
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        try {
            $uuid = UuidHelper::uuid();
            $code = md5($this->channel . $message);

            $this->db->createCommand()->insert($this->tableName, [
                'id' => $uuid,
                'code' => $code,
                'channel' => $this->channel,
                'job' => $message,
                'pushedAt' => time(),
                'ttr' => $ttr,
                'delay' => $delay,
                'priority' => $priority ?: 1024,
            ])->execute();

            return $uuid;
        } catch (\Exception $e) {
            $valid = false;
            Yii::debug($e);
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function status($id)
    {
        $payload = (new Query())
            ->from($this->tableName)
            ->where(['id' => $id])
            ->one($this->db);

        if (!$payload) {
            if ($this->deleteReleased) {
                return self::STATUS_DONE;
            }

            throw new InvalidArgumentException("Unknown message ID: $id.");
        }

        if (!$payload['reservedAt']) {
            return self::STATUS_WAITING;
        }

        if (!$payload['doneAt']) {
            return self::STATUS_RESERVED;
        }

        return self::STATUS_DONE;
    }

    /**
     * Takes one message from waiting list and reserves it for handling.
     *
     * @return array|false payload
     * @throws Exception in case it hasn't waited the lock
     */
    protected function reserve()
    {
        return $this->db->useMaster(function () {
            if (!$this->mutex->acquire(__CLASS__ . $this->channel, $this->mutexTimeout)) {
                throw new Exception('Has not waited the lock.');
            }

            try {
                $this->moveExpired();

                // Reserve one message
                $payload = (new Query())
                    ->from($this->tableName)
                    ->andWhere(['channel' => $this->channel, 'reservedAt' => null])
                    ->andWhere('[[pushedAt]] <= :time - [[delay]]', [':time' => time()])
                    ->orderBy(['priority' => SORT_ASC, 'id' => SORT_ASC])
                    ->limit(1)
                    ->one($this->db);
                if (is_array($payload)) {
                    $payload['reservedAt'] = time();
                    $payload['attempt'] = (int) $payload['attempt'] + 1;
                    $this->db->createCommand()->update($this->tableName, [
                        'reservedAt' => $payload['reservedAt'],
                        'attempt' => $payload['attempt'],
                    ], [
                        'id' => $payload['id'],
                    ])->execute();

                    // pgsql
                    if (is_resource($payload['job'])) {
                        $payload['job'] = stream_get_contents($payload['job']);
                    }
                }
            } finally {
                $this->mutex->release(__CLASS__ . $this->channel);
            }

            return $payload;
        });
    }



    /**
     * @param array $payload
     */
    protected function release($payload)
    {
        if ($this->deleteReleased) {
            $this->db->createCommand()->delete(
                $this->tableName,
                ['id' => $payload['id']]
            )->execute();
        } else {
            $this->db->createCommand()->update(
                $this->tableName,
                ['doneAt' => time()],
                ['id' => $payload['id']]
            )->execute();
        }
    }

    /**
     * Moves expired messages into waiting list.
     */
    protected function moveExpired()
    {
        if ($this->reserveTime !== time()) {
            $this->reserveTime = time();
            $this->db->createCommand()->update(
                $this->tableName,
                ['reservedAt' => null],
                // `reservedAt IS NOT NULL` forces db to use index on column,
                // otherwise a full scan of the table will be performed
                '[[reservedAt]] is not null and [[reservedAt]] < :time - [[ttr]] and [[doneAt]] is null',
                [':time' => $this->reserveTime]
            )->execute();
        }
    }
}