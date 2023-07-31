<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use yii\base\InvalidConfigException;
use yii\bootstrap4\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Tabs
 * @package common\widgets
 */
class Tabs extends \yii\bootstrap4\Tabs
{
    public $navType = 'nav-pills';

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function run()
    {
        $this->registerPlugin('tab');
        $this->prepareItems($this->items);

        $html = Html::beginTag('div', ['class' => 'card mb-0 w-100 h-100']);

        $html .= Html::beginTag('div', ['class' => 'card-header p-2']);
        $html .= $this->renderNav();
        $html .= Html::endTag('div');

        $html .= Html::beginTag('div', ['class' => 'card-body p-0']);
        $html .= $this->renderPanes($this->panes);
        $html .= Html::endTag('div');

        $html .= Html::endTag('div');

        return $html;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function renderNav()
    {
        return Nav::widget([
            'dropdownClass' => $this->dropdownClass,
            'options' => ArrayHelper::merge(['role' => 'tablist'], $this->options),
            'items' => $this->items,
            'encodeLabels' => $this->encodeLabels,
        ]);
    }
}