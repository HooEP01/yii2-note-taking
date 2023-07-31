<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\caching;

use yii\base\InvalidConfigException;
use yii\caching\Cache;
use Yii;

/**
 * Class MainCache
 * @package common\base\caching
 */
class MainCache extends Cache
{
    /**
     * @var string
     */
    public $cache = 'fileCache';

    /**
     * @var Cache
     */
    private $_cache;

    /**
     * @param string|array $value
     * @throws InvalidConfigException
     */
    protected function setCache($value)
    {
        if (is_array($value) || is_string($value)) {
            $this->cache = $value;
        } else {
            throw new InvalidConfigException('cache must be string or array !');
        }
    }

    /**
     * @return Cache
     * @throws InvalidConfigException
     */
    protected function getCacheComponent()
    {
        if (isset($this->_cache)) {
            return $this->_cache;
        }

        Yii::debug(__METHOD__);

        if (is_string($this->cache)) {
            /** @var Cache $cache */
            if (($cache = Yii::$app->get($this->cache)) instanceof Cache) {
                return $this->_cache = $cache;
            }
        }

        if (is_array($this->cache)) {
            $cache = Yii::createObject($this->cache);
            if ($cache instanceof Cache) {
                return $this->_cache = $cache;
            }
        }

        throw new InvalidConfigException('cache is not configure correctly !');
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function getOrSet($key, $callback, $duration = 0, $dependency = null)
    {
        return $this->getCacheComponent()->getOrSet($key, $callback, $duration, $dependency);
    }

    /**
     * @inheritdoc
     * @param string $key a unique key identifying the cached value
     * @return string|boolean the value stored in cache, false if the value is not in the cache or expired.
     * @throws InvalidConfigException
     */
    protected function getValue($key)
    {
        Yii::debug('getSystemCache: ' . $key);
        return $this->getCacheComponent()->getValue($key);
    }

    /**
     * @inheritdoc
     * @param string $key       the key identifying the value to be cached
     * @param string $value     the value to be cached
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     * @throws InvalidConfigException
     */
    protected function setValue($key, $value, $duration)
    {
        Yii::debug('setSystemCache: ' . $key);
        return $this->getCacheComponent()->setValue($key, $value, $duration);
    }

    /**
     * @inheritdoc
     * @param string $key       the key identifying the value to be cached
     * @param string $value     the value to be cached
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     * @throws InvalidConfigException
     */
    protected function addValue($key, $value, $duration)
    {
        return $this->getCacheComponent()->addValue($key, $value, $duration);
    }

    /**
     * @inheritdoc
     * @param string $key the key of the value to be deleted
     * @return boolean if no error happens during deletion
     * @throws InvalidConfigException
     */
    protected function deleteValue($key)
    {
        return $this->getCacheComponent()->deleteValue($key);
    }

    /**
     * @inheritdoc
     * @return boolean whether the flush operation was successful.
     * @throws InvalidConfigException
     */
    protected function flushValues()
    {
        return $this->getCacheComponent()->flushValues();
    }
}
