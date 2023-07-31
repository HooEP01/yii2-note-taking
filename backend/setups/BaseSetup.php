<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\setups;

use common\base\enum\ConfigType;
use common\base\services\Config;
use common\base\services\Sanitizer;
use yii\base\Model;
use yii\di\Instance;
use Yii;

/**
 * Class Base
 *
 * @package backend\setups
 */
abstract class BaseSetup extends Model
{
    /**
     * @var string
     */
    public $prefix;

    /**
     * @var Config
     */
    public $config = 'config';

    /**
     * initialize
     */
    public function init()
    {
        parent::init();

        $this->config = Instance::ensure($this->config, Config::class);

        foreach ($this->getAttributeKeyMaps() as $attribute => $map) {
            $this->{$attribute} = $this->config->get($map['key']);
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $valid = $this->beforeProcess();
        $valid = $valid && $this->process();
        foreach ($this->getAttributeKeyMaps() as $attribute => $map) {
            $valid = $valid && $this->config->set($map['key'], $this->{$attribute}, $map['type']);
        }
        return $valid;
    }

    /**
     * @return bool
     */
    protected function process()
    {
        return $this->validate();
    }

    /**
     * @return boolean
     */
    protected function beforeProcess()
    {
        return true;
    }

    /**
     * @param $value
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     */
    public function stripClean($value)
    {
        /** @var Sanitizer $sanitizer
         */
        $sanitizer = Yii::$app->get('sanitizer');
        return $sanitizer->stripClean($value);
    }

    /**
     * @return array
     */
    protected function getAttributeKeyMaps()
    {
        $maps = $this->getConfigMaps();
        if (!empty($this->prefix)) {
            foreach ($maps as $attribute => $key) {
                $maps[$attribute] = sprintf($key, $this->prefix);
            }
        }

        $keyMaps = [];
        foreach ($maps as $attribute => $key) {
            $field = $attribute;
            $type = ConfigType::RAW;

            if (($pos = strpos($attribute, ':')) !== false) {
                list($field, $type) = explode(':', $attribute, 2);
            }
            $keyMaps[$field] = ['type' => $type, 'key' => $key];
        }
        return $keyMaps;
    }

    /**
     * @return array
     */
    abstract protected function getConfigMaps();
}
