<?php
/**
 * @author RYU Chua <ryu@riipay.my>
 * @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace common\base\behaviors;

use common\base\enum\ConfigType;
use common\base\helpers\ArrayHelper;
use common\base\helpers\Json;
use common\base\traits\RuntimeCache;
use common\models\common\BaseConfiguration;
use common\models\ModelConfig;
use yii\base\Behavior;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class ConfigBehavior
 *  @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class ConfigBehavior extends Behavior
{
    use RuntimeCache;

    public $cacheAttribute = 'configuration';

    public $typeMap = [
        '_debug' => ConfigType::STRING,
    ];

    public $map = [];

    /**
     * @param array $options
     * @return BaseConfiguration
     */
    public function getConfigurationModel($options = [])
    {
        if (empty($this->map) || !is_array($this->map)) {
            throw new InvalidConfigException('$map cannot be empty and must be array!');
        }

        $key = (string) ArrayHelper::getValue($options, ['key'], 'default');
        if (!array_key_exists($key, $this->map)) {
            throw new InvalidConfigException(sprintf('Key "%s" not exist in $map', $key));
        }

        $cacheKey = md5(__METHOD__ . 'v1' . @serialize($key));
        $configuration = $this->getOrSetRuntimeData($cacheKey, function () use ($key, $options) {
            $useCache = ArrayHelper::getValue($options, ['useCache'], true);

            $class = ArrayHelper::getValue($this->map, [$key, 'class']);
            if (empty($class)) {
                throw new InvalidConfigException('class is required in $config !');
            }

            $config = ['class' => $class, 'model' => $this->owner];
            if ($useCache) {
                $config['values'] = (array) $this->owner->getAttribute($this->cacheAttribute);
            }

            return Yii::createObject($config);
        });

        if ($configuration instanceof BaseConfiguration) {
            return $configuration;
        }

        throw new InvalidCallException('No instance of BaseConfiguration created !');
    }

    /**
     * @param string $code
     * @param mixed $value
     * @return bool
     */
    public function setModelConfigValue($code, $value = null)
    {
        $code = trim($code);
        if (!array_key_exists($code, $this->typeMap)) {
            return false;
        }

        $m = $this->getModelConfigByCode($code);
        if ($m === null) {
            $m = ModelConfig::factory($this->owner);
            $m->name = $code;
        }

        $type = ArrayHelper::getValue($this->typeMap, $code);
        if ($type === null || !in_array($type, ConfigType::values())) {
            $type = ConfigType::RAW;
        }

        if ($type === ConfigType::ARRAY && is_array($value)) {
            $value = Json::encode($value);
        }

        $m->type = $type;
        $m->content = $value;
        if ($type === ConfigType::BOOLEAN) {
            $m->content = (int) $value;
        }

        if ($m->save()) {
            return true;
        } elseif ($m->hasErrors()) {
            Yii::debug($m->errors);
        }

        return false;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getModelConfigValue($code)
    {
        $code = trim($code);
        if (($m = $this->getModelConfigByCode($code)) instanceof ModelConfig) {
            return $m->getValue();
        }
        return null;
    }

    /**
     * @param string $code
     * @return array|ModelConfig|ActiveRecord|null
     */
    protected function getModelConfigByCode($code)
    {
        if (array_key_exists($code, $this->typeMap)) {
            return ModelConfig::find()->alias('t')
                ->owner($this->owner)
                ->name($code)
                ->limit(1)
                ->one();
        }

        return null;
    }
}
