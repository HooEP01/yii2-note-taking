<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Country
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'list' => ['label' => Yii::t('backend', 'common.tab.list'), 'url' => ['/country/list']],
    'create' => ['label' => Yii::t('backend', 'country.tab.create'), 'url' => '#', 'visible' => ($current === 'create')],
];

$showUpdateTab = in_array($current, ['update', 'image']);
if ($showUpdateTab) {
    $items['update'] = ['label' => $model->name, 'url' => '#', 'visible' => $showUpdateTab];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

if ($showUpdateTab) {
    ArrayHelper::remove($items['update'], 'url');
    $items['update']['active'] = true;
    $items['update']['content'] = $content;
}

echo Tabs::widget(['items' => $items]);