<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\services;

use common\base\enum\ConfigName;
use common\base\enum\ConfigType;
use common\base\traits\RuntimeCache;
use common\models\SystemConfig;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\caching\Cache;
use Yii;

/**
 * Class Config
 * @package common\base\services
 */
class Config extends BaseObject
{
    use RuntimeCache;

    /**
     * @var string|Cache the cache component id or configuration
     */
    public $cache = 'cache';
    /**
     * @var int
     */
    public $cacheDuration = 2592000;
    /**
     * @var string
     */
    public $prefix = 'system.config.';

    /**
     * ensure some instance type
     */
    public function init()
    {
        parent::init();

        $cache = is_string($this->cache) ? Yii::$app->get($this->cache, false) : $this->cache;
        if ($cache instanceof Cache) {
            $this->cache = $cache;
        } else {
            throw new Exception('cache is not an instance of yii\caching\Cache');
        }
    }

    /**
     * @return mixed|null
     */
    public function getImageMaximumSize()
    {
        return $this->get(ConfigName::UPLOAD_IMAGE_MAXIMUM_SIZE, 1024 * 1024 * 4);
    }

    /**
     * @return string
     */
    public function getImageUploaderHint()
    {
        $maxSize = $this->getImageMaximumSize();
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return sprintf("Type: %s\nMax Size: %s", implode(', ', $extensions), Yii::$app->formatter->asShortSize($maxSize));
    }

    /**
     * @return mixed|null
     */
    public function getDocumentMaximumSize()
    {
        return $this->get(ConfigName::UPLOAD_DOCUMENT_MAXIMUM_SIZE, 1024 * 1024 * 4);
    }

    /**
     * @return string
     */
    public function getDocumentUploaderHint()
    {
        $maxSize = $this->getDocumentMaximumSize();
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'docx', 'doc', 'xls', 'xlsx', 'zip'];

        return sprintf("Type: %s\nMax Size: %s", implode(', ', $extensions), Yii::$app->formatter->asShortSize($maxSize));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->hasRuntimeData($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return boolean
     */
    public function set($key, $value, $type = null)
    {
        if (($model = SystemConfig::find()->name($key)->limit(1)->one()) === null) {
            $model = new SystemConfig();
            $model->name = trim($key);
        }

        if ($type === null || !in_array($type, ConfigType::values())) {
            $type = ConfigType::RAW;
        }

        $model->type = $type;
        $model->content = $value;
        if ($model->save()) {
            $this->setRuntimeData($key, $value);
            $cacheKey = $this->buildCacheKey($key);
            $this->cache->set($cacheKey, $value, $this->cacheDuration);
            return true;
        } else {
            Yii::debug($model->errors);
        }

        return false;
    }

    /**
     * get the value of for system
     * @param string $key
     * @param null|mixed $defaultValue
     * @return null|mixed
     */
    public function get($key, $defaultValue = null)
    {
        if ($this->has($key)) {
            return $this->getRuntimeData($key);
        }

        $cacheKey = $this->buildCacheKey($key);
        if (($data = $this->cache->get($cacheKey)) === false) {
            if (($model = SystemConfig::find()->name($key)->limit(1)->one()) instanceof SystemConfig) {
                $data = $model->getIsScalar() ? $model->getValue() : false;
            } else {
                $data = $defaultValue;
            }
            $this->cache->set($cacheKey, $data, $this->cacheDuration);
        }

        $this->setRuntimeData($key, $data);
        return $data;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function buildCacheKey($key)
    {
        return $this->prefix . md5($key);
    }
}
