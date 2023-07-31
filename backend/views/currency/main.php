<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\CurrencyRate
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

/** @var \backend\controllers\CurrencyController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['subtitle'] = $controller->getName();
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'list' => ['label' => Yii::t('backend', 'common.tab.list'), 'url' => ['/currency/list']],
    'create' => ['label' => Yii::t('backend', 'currency.tab.create'), 'url' => '#', 'visible' => ($current === 'create')],
    'update' => ['label' => Yii::t('backend', 'currency.tab.update'), 'url' => '#', 'visible' => ($current === 'update')],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget(['items' => $items]);

