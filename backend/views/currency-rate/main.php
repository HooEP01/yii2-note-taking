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

/** @var \backend\controllers\CurrencyRateController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

$current = isset($current) ? $current : $this->getRouteName();
$items = [
    'currency-rate.list' => ['label' => Yii::t('backend', 'common.tab.list'), 'url' => ['/currency-rate/list']],
    'currency-rate.create' => ['label' => Yii::t('backend', 'currency_rate.tab.create'), 'url' => '#', 'visible' => ($current === 'currency-rate.create')],
    'currency-rate.update' => ['label' => Yii::t('backend', 'currency_rate.tab.update'), 'url' => '#', 'visible' => ($current === 'currency-rate.update')],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items,
]);

