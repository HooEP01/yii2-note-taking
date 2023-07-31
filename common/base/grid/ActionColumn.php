<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\grid;

use common\base\db\ActiveRecord;
use common\base\helpers\ArrayHelper;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use Yii;

/**
 * Class ActionColumn
 * @package common\base\grid
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * @var string
     */
    public $template = '{view} {update} {delete}';

    /**
     * @var array
     */
    public $headerOptions = ['class' => 'action-column', 'style' => 'text-align: center; width: 90px'];

    /**
     * @var array
     */
    public $contentOptions = ['style' => 'text-align: center'];

    /**
     * @var array
     */
    public $buttonOptions = ['class' => 'grid-link'];
    /**
     * @var array
     */
    public $dropdownItems = [];
    /**
     * @var array
     */
    public $dropdownButtons = [];

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', 'far fa-eye');
        $this->initDefaultButton('update', 'far fa-edit');
        $this->initDefaultButton('delete', 'far fa-trash-alt', [
            'data-confirm' => Yii::t('backend', 'model.delete.confirmation'),
            'data-method' => 'post',
        ]);
        $this->initDefaultButton('image', 'fas fa-image');
        $this->initDefaultButton('toggle', 'fas fa-retweet', [
            'data-confirm' => Yii::t('backend', 'model.toggle.confirmation'),
            'data-method' => 'post',
        ]);

        $this->initDefaultDropdownButton('toggle', 'fas fa-retweet', [
            'data-confirm' => Yii::t('backend', 'model.toggle.confirmation'),
            'data-method' => 'post',
        ]);
    }

    /**
     * Initializes the default button rendering callback for single button.
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, ActiveRecord $model, $key) use ($name, $iconName, $additionalOptions) {
                //custom icon for toggle
                switch ($name) {
                    case 'toggle' :
                        if ($model->getIsActive()) {
                            $iconName = 'far fa-trash-alt';
                            $additionalOptions['data-confirm'] = Yii::t('backend', 'model.delete.confirmation');
                        } else {
                            $iconName = 'fas fa-undo';
                            $additionalOptions['data-confirm'] = Yii::t('backend', 'model.restore.confirmation');
                        }
                        break;
                }
                $icon = Html::tag('i', '', ['class' => $iconName]);
                $title = ucfirst($name);
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                Html::addCssClass($options, ['color-black']);
                return Html::a($icon, $url, $options);
            };
        }
    }

    /**
     * @param string $name
     * @param string $iconName
     * @param array $linkOptions
     */
    protected function initDefaultDropdownButton($name, $iconName, $linkOptions = [])
    {
        if (!isset($this->dropdownButtons[$name])) {
            $this->dropdownButtons[$name] = function ($url, ActiveRecord $model, $key) use ($name, $iconName, $linkOptions) {
                $title = ucfirst($name);
                if ($name === 'toggle') {
                    if ($model->getIsActive()) {
                        $iconName = 'far fa-trash-alt';
                        $linkOptions['data-confirm'] = Yii::t('backend', 'model.delete.confirmation');
                        $title = 'Delete';
                    } else {
                        $iconName = 'fas fa-undo';
                        $linkOptions['data-confirm'] = Yii::t('backend', 'model.restore.confirmation');
                        $title = 'Restore';
                    }
                }

                $label = Html::tag('i', '', ['class' => $iconName]) . Html::tag('span', $title, ['class' => 'ml-3']);
                return [
                    'label' => $label,
                    'url' => [$name, 'id' => $key],
                    'encode' => false,
                    'options' => ['class' => 'grid-link'],
                    'linkOptions' => $linkOptions
                ];
            };
        }
    }

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int   $index
     * @return string|string[]|null
     * @throws \Exception
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        if (!empty($this->dropdownItems)) {
            return $content . ButtonDropdown::widget([
                'direction' => ButtonDropdown::DIRECTION_LEFT,
                'buttonOptions' => ['class' => 'btn-sm btn-more grid-link'],
                'label' => '<i class="fas fa-ellipsis-v"></i>',
                'encodeLabel' => false,
                'options' => ['class' => 'ml-1'],
                'dropdown' => [
                    'items' => $this->renderDropdownItems($model, $key, $index),
                ]
            ]);
        }

        return $content;
    }

    /**
     * @param $model
     * @return array
     */
    protected function renderDropdownItems($model, $key, $index)
    {
        $items = [];
        foreach ($this->dropdownItems as $action => $dropdownItem) {
            $url = $this->createUrl($action, $model, $key, $index);
            if (is_string($dropdownItem)) {
                if (isset($this->dropdownButtons[$dropdownItem])) {
                    $items[] = call_user_func($this->dropdownButtons[$dropdownItem], $url, $model, $key);
                }
            } else {
                $items[] = call_user_func($dropdownItem, $url, $model, $key);
            }
        }
        return $items;
    }
}