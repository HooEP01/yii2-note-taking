<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $model \common\models\UserSocial
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();

$items = [
    'social' => ['label' => Yii::t('backend', 'breadcrumb.list'), 'url' => ['/user/social', 'id' => $user->id]],
    'social-create' => ['label' =>  Yii::t('backend', 'user.tab.social_create'), 'url' => '#', 'visible' => ($current === 'social-create')],
];

if (isset($model)) {
    $items['social-update'] = ['label' => Yii::t('backend', 'user.tab.social_update'), 'url' => '#', 'visible' => ($current === 'social-update')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);

