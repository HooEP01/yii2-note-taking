<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $model \common\models\UserEmail
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();

$items = [
    'email' => ['label' => Yii::t('backend', 'breadcrumb.list'), 'url' => ['/user/email', 'id' => $user->id]],
    'email-create' => ['label' =>  Yii::t('backend', 'user.tab.email_create'), 'url' => '#', 'visible' => ($current === 'email-create')],
];

if (isset($model)) {
    $items['email-update'] = ['label' => Yii::t('backend', 'user.tab.email_update'), 'url' => '#', 'visible' => ($current === 'email-update')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);

