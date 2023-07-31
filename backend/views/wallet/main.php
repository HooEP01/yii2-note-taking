<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $model \common\models\Wallet
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getRouteName();

$items = [
    'user.wallet' => ['label' => Yii::t('backend', 'common.tab.overview'), 'url' => ['/user/wallet', 'id' => $user->id]],
];

if (isset($model)) {
    $items['wallet.view'] = ['label' => $model->currency->name, 'url' => ['/wallet/view', 'id' => $model->id]];
    $items['wallet.adjust'] = ['label' => Yii::t('backend', 'breadcrumb.wallet.adjust_{name}', ['name' => $model->currency->name]), 'url' => '#', 'visible' => ($current === 'wallet.adjust')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);
