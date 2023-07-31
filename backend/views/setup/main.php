<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use yii\bootstrap4\Tabs;

/** @var \backend\base\web\Controller $controller */
$controller = $this->context;

$current = isset($current) ? $current : $this->getRouteName();
$items = [
    'setup.general'  => ['label' => Yii::t('backend', 'common.tab.general'), 'url' => ['/setup/general']],
    'setup.mailer' => ['label' => Yii::t('backend', 'setup.tab.mailer'), 'url' => ['/setup/mailer']],

    'currency.list' => ['label' => Yii::t('backend', 'breadcrumb.currency'), 'url' => ['/currency/list']],
    'currency-rate.list' => ['label' => Yii::t('backend', 'breadcrumb.currency_rate'), 'url' => ['/currency-rate/list']],
    'country.list' => ['label' => Yii::t('backend', 'breadcrumb.country'), 'url' => ['/country/list']],
    'state.list' => ['label' => Yii::t('backend', 'breadcrumb.state'), 'url' => ['/state/list']],
    'city.list' => ['label' => Yii::t('backend', 'breadcrumb.city'), 'url' => ['/city/list']],
    'language.list' => ['label' => Yii::t('backend', 'breadcrumb.language'), 'url' => ['/language/list']],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

if (in_array($controller->id, ['currency', 'currency-rate', 'country', 'state', 'city', 'language', 'agent-subscription', 'amenity', 'build-status'])) {
    $current = sprintf('%s.list', $controller->id);
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

?>

<div class="row">
    <div class="col-5 col-sm-4 col-md-2 pr-0">
        <?= Tabs::widget([
            'navType' => 'flex-column nav-tabs h-100 nav-danger',
            'renderTabContent' => false,
            'items' => $items,
        ]); ?>
    </div>
    <div class="col-7 col-sm-8 col-md-10 pl-0">
        <div class="tab-content">
            <div class="tab-pane active">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>




