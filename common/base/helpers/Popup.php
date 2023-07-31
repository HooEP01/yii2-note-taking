<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use yii\base\BaseObject;
use yii\helpers\Html;

/**
 * Class Popup
 * @package common\base\helpers
 */
class Popup extends BaseObject
{
    public $text = 'view';
    public $width = 1600;
    public $height = 1024;
    public $options = [];
    public $params = [];
    public $url;

    /**
     * @param array $options
     * @return static
     */
    public static function factory($options = [])
    {
        $popup = new static();
        if (!empty($text = ArrayHelper::remove($options, 'text'))) {
            $popup->text($text);
        }

        if (!empty($url = ArrayHelper::remove($options, 'url'))) {
            $popup->url($url);
        }

        if (!empty($width = ArrayHelper::remove($options, 'width'))) {
            $popup->width($width);
        }

        if (!empty($height = ArrayHelper::remove($options, 'height'))) {
            $popup->height($height);
        }

        $popup->options($options);

        return $popup;
    }

    /**
     * @param $value
     * @param $replace
     * @return $this
     */
    public function options($value, $replace = false)
    {
        if ($replace) {
            $this->options = $value;
        } else {
            $this->options = ArrayHelper::merge($this->options, $value);
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function width($value)
    {
        $this->width = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function height($value)
    {
        $this->height = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function text($value)
    {
        $this->text = $value;
        return $this;
    }

    /**
     * @param $value
     * @param string $formName
     */
    public function params($value, $formName = '')
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (!empty($formName)) {
                    ArrayHelper::setValue($this->params, [$formName, $k], $v);
                } else {
                    ArrayHelper::setValue($this->params, $k, $v);
                }
            }
        }

        return $this;
    }

    /**
     * @param string|array $value
     * @param boolean $scheme
     * @return $this
     */
    public function url($value, $scheme = false)
    {
        $url = Url::to($value, $scheme);
        $url .= strpos($url, '?') === false ? '?' : '&';
        $url .= http_build_query(['_popup' => 1]);

        if (!empty($this->params)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= http_build_query($this->params);
        }

        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $linkOptions = [
            'onclick' => sprintf('window.open("%s", "popup", "width=%s,height=%s,top=100,left=100"); return false;', $this->url, $this->width, $this->height),
        ];

        $options = ArrayHelper::merge($this->options, $linkOptions);

        return Html::a($this->text, 'javascript:void()', $options);
    }
}