<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\State
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

/** @var \backend\controllers\StateController $controller */
$controller = $this->context;

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'list' => ['label' => Yii::t('backend', 'common.tab.list'), 'url' => ['/state/list']],
    'create' => ['label' => Yii::t('backend', 'state.tab.create'), 'url' => '#', 'visible' => ($current === 'create')],
];

if (isset($model)) {
    $items['update'] = ['label' => $model->name, 'url' => '#', 'visible' => ($current === 'update')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget(['items' => $items]);