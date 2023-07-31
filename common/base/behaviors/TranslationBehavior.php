<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use common\base\enum\LanguageCode;
use common\base\helpers\ArrayHelper;
use common\models\ModelTranslation;
use yii\base\Behavior;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\helpers\Html;
use yii\web\Request;
use Yii;

/**
 * Class TranslationBehavior
 * @property ActiveRecord $owner
 * @property string $baseLanguage
 * @package alpstein\yii\behaviors
 */
class TranslationBehavior extends Behavior
{
    /**
     * @var string|Cache
     */
    public $cache = 'cache';
    /**
     * @var array
     */
    public $attributes = [];
    /**
     * @var string
     */
    public $language;
    /**
     * @var string
     */
    public $languageParam = 'language';
    /**
     * @var string
     */
    public $languageHeader = 'x-edit-language';
    /**
     * @var string
     */
    public $baseLanguageAttribute;
    /**
     * whether to load default when empty
     * @var boolean
     */
    public $fallbackEmpty = false;
    /**
     * to enforce using system language
     * @var boolean
     */
    public $useApplicationLanguage = false;
    /**
     * @var array
     */
    private $_cacheOriginals = [];
    /**
     * @var string
     */
    private $_baseLanguage;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'translate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    /**
     * initialize
     */
    public function init()
    {
        parent::init();

        $this->cache = Instance::ensure($this->cache, Cache::class);
        $this->initializeLanguage();
    }

    /**
     * set the option mode and re-translate
     */
    public function setOptionMode()
    {
        $this->fallbackEmpty = true;
        $this->useApplicationLanguage = true;
        $this->initializeLanguage();
        $this->translate();
    }

    /**
     * initialize the $language variable
     */
    public function initializeLanguage()
    {
        if ($this->useApplicationLanguage) {
            $this->language = LanguageCode::resolveCode(Yii::$app->language);
            return;
        }

        if (!isset($this->language) && ($request = Yii::$app->request) instanceof Request) {
            if (($language = $request->getQueryParam($this->languageParam)) !== null) {
                $this->language = LanguageCode::resolveCode($language, false);
            }

            if (empty($this->language) && ($language = $request->headers->get($this->languageHeader)) !== null) {
                $this->language = LanguageCode::resolveCode($language, false);
            }
        }

        if (empty($this->language)) {
            $this->language = LanguageCode::ENGLISH;
        }
    }

    /**
     * @return string
     */
    public function getBaseLanguage()
    {
        if (isset($this->_baseLanguage)) {
            return $this->_baseLanguage;
        }

        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if (isset($this->baseLanguageAttribute) && $owner->hasAttribute($this->baseLanguageAttribute)) {
            return $this->_baseLanguage = LanguageCode::resolveCode($owner->getAttribute($this->baseLanguageAttribute));
        }

        return $this->_baseLanguage = LanguageCode::ENGLISH;
    }

    /**
     * @return bool
     */
    public function getIsDefaultLanguage()
    {
        return $this->language == LanguageCode::ENGLISH;
    }

    /**
     * @param string $language
     */
    public function changeLanguage($language)
    {
        if (in_array($language, LanguageCode::getSupported())) {
            $this->language = $language;
            $this->translate();
        }
    }

    /**
     * @return string
     */
    public function getLanguageName()
    {
        return LanguageCode::resolveName($this->language);
    }

    /**
     * @return string
     */
    public function getModelLanguageCode()
    {
        return $this->language;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getTranslationColumnValue($attribute)
    {

        if (in_array($attribute, $this->attributes)) {
            $original = $this->language;

            $values = [];
            foreach (LanguageCode::getSupported() as $code) {
                $this->changeLanguage($code);
                $value = ArrayHelper::getValue($this->owner, $attribute);
                if (empty($value)) {
                    $value = Yii::$app->formatter->nullDisplay;
                }

                $values[] = Html::tag('strong', strtoupper($code))  . ': ' . $value;
            }

            $this->changeLanguage($original);

            return implode('<br />', $values);
        }

        return ArrayHelper::getValue($this->owner, $attribute);
    }

    /**
     * @param $attribute
     * @return string
     */
    protected function generateMessage($attribute)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $class = $owner->tableName();
        $class = str_replace(['{', '}', '%'], '', $class);
        $key = implode('-', $owner->getPrimaryKey(true));
        return sprintf('%s.%s.%s', $class, $attribute, $key);
    }

    /**
     * @return string
     */
    protected function generateCacheKey()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        return sprintf('%s.%s.%s', $owner->tableName(), implode('-', $owner->getPrimaryKey(true)), $this->language) . '-v1';
    }

    /**
     * reset the attributes to original
     */
    public function resetToOriginal()
    {
        $this->debug($this->_cacheOriginals);
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach ($this->_cacheOriginals as $attribute => $value) {
            $owner->setAttribute($attribute, $value);
        }
    }

    /**
     * re-translate
     * @return void
     */
    public function afterUpdate()
    {
        if ($this->language !== $this->baseLanguage) {
            $this->translate();
        }
    }

    /**
     * translate the related fields
     */
    public function translate()
    {
        $this->debug(sprintf('Language: %s, Base Language: %s', $this->language, $this->baseLanguage));
        $this->resetToOriginal();
        if ($this->language === $this->baseLanguage) {
            return;
        }

        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $maps = [];
        foreach ($this->attributes as $attribute) {
            $message = $this->generateMessage($attribute);
            $maps[md5($message)] = ['attribute' => $attribute, 'message' => $message];

            $this->_cacheOriginals[$attribute] = $owner->getAttribute($attribute);

            if ($this->fallbackEmpty === false) {
                $owner->setAttribute($attribute, '');
            }
        }

        $translations = Yii::$app->cache->getOrSet($this->generateCacheKey(), function () use ($maps) {
            $messages = ArrayHelper::getColumn($maps, 'message');
            return ModelTranslation::find()
                ->alias('t')
                ->select(['t.message', 't.content'])
                ->language($this->language)
                ->message($messages)
                ->indexBy('message')
                ->asArray()
                ->all();
        });

        foreach ($translations as $message => $row) {
            $key = md5($message);
            if (isset($maps[$key])) {
                $content = trim($row['content']);
                if (!empty($content)) {
                    $owner->setAttribute($maps[$key]['attribute'], $row['content']);
                }
            }
        }
    }

    /**
     * before update
     */
    public function beforeUpdate()
    {
        if ($this->generateTranslations()) {
            $this->resetToOriginal();
            return true;
        }

        return false;
    }

    /**
     * after insert
     */
    public function afterInsert()
    {
        return $this->generateTranslations();
    }

    /**
     * generate translation values
     * @return bool
     */
    protected function generateTranslations()
    {
        $valid = true;
        if ($this->language !== $this->baseLanguage) {
            /** @var ActiveRecord $owner */
            $owner = $this->owner;
            foreach ($this->attributes as $attribute) {
                $message = $this->generateMessage($attribute);
                $translation = ModelTranslation::find()
                    ->language($this->language)
                    ->message($message)->one();

                if ($translation === null) {
                    $translation = new ModelTranslation(['language' => $this->language, 'message' => $message]);
                    $translation->code = md5($message);
                    $translation->ownerType = $owner->tableName();
                    $translation->ownerKey = implode('-', $owner->getPrimaryKey(true));
                    $translation->ownerAttribute = $attribute;
                }

                $translation->content = $owner->getAttribute($attribute);
                $valid = $valid && $translation->save();
                $valid && Yii::$app->cache->delete($this->generateCacheKey());
            }
        }

        return $valid;
    }

    /**
     * @param mixed $message
     * @param null $category
     */
    protected function debug($message, $category = null)
    {
        if (!YII_DEBUG) {
            return;
        }

        if ($category === null) {
            $category = get_called_class();
        }

        Yii::debug($message, $category);
    }
}
