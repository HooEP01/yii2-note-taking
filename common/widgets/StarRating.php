<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class StarRating
 * @package backend\base\widgets
 */
class StarRating extends Widget
{
    /**
     * @var string
     */
    public $rating;

    /**
     * @var string
     */
    public $fullIconClass;

    /**
     * @var string
     */
    public $emptyIconClass;

    /**
     * @var string
     */
    public $style;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset($this->rating)) {
            throw new InvalidConfigException('$rating must be set!');
        }
        if (!isset($this->style)) {
            $this->style = 'color: #f0ad4e; font-size: 20px; margin-right: 2px';
        }
        if (!isset($this->fullIconClass)) {
            $this->fullIconClass = 'fas fa-star';
        }
        if (!isset($this->emptyIconClass)) {
            $this->emptyIconClass ='far fa-star';
        }
    }

    /**
     * @return string|void
     */
    public function run()
    {
        Html::beginTag('span');
        for ($i = 1; $i <= $this->rating; $i++) {
            echo Html::tag('i', '', ['class' => $this->fullIconClass, 'style' => $this->style]);
        }

        if (($diff = abs(5 - $this->rating)) > 0) {
            for ($i = 0; $i < $diff; $i++) {
                echo Html::tag('i', '', ['class' => $this->emptyIconClass, 'style' => $this->style]);
            }
        }
        Html::endTag('span');
    }
}