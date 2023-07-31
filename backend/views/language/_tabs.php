<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Language
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

/** @var \backend\controllers\LanguageController $controller */
$controller = $this->context;

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'update' => ['label' => Yii::t('backend', 'common.tab.basic'), 'url' => ['/language/update', 'code' => $model->code]],
    'image' => ['label' => Yii::t('backend', 'common.tab.image'), 'url' => ['/language/image', 'code' => $model->code]],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
}

echo Tabs::widget([
    'navType' => 'flex-column nav-pills h-100',
    'items' => $items
]);
