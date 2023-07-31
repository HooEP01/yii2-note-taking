<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $model \common\models\Address
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();

$items = [
    'address' => ['label' => Yii::t('backend', 'breadcrumb.list'), 'url' => ['/user/address', 'id' => $user->id]],
    'address-create' => ['label' =>  Yii::t('backend', 'user.tab.address_create'), 'url' => '#', 'visible' => ($current === 'address-create')],
];

if (isset($model)) {
    $items['address-update'] = ['label' => Yii::t('backend', 'user.tab.address_update'), 'url' => '#', 'visible' => ($current === 'address-update')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);

