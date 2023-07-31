<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $model \common\models\UserPhone
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();

$items = [
    'phone' => ['label' => Yii::t('backend', 'breadcrumb.list'), 'url' => ['/user/phone', 'id' => $user->id]],
    'phone-create' => ['label' =>  Yii::t('backend', 'user.tab.phone_create'), 'url' => '#', 'visible' => ($current === 'phone-create')],
];

if (isset($model)) {
    $items['phone-update'] = ['label' => Yii::t('backend', 'user.tab.phone_update'), 'url' => '#', 'visible' => ($current === 'phone-update')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);

