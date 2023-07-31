<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $this \backend\base\web\View
 * @var $content string
 */

use common\base\enum\LanguageCode;
use common\base\helpers\ArrayHelper;
use yii\bootstrap4\Tabs;
use yii\helpers\Url;

/** @var \backend\base\web\Controller $controller */
$controller = $this->context;
$current = $controller->getLanguage();

$isNew = isset($isNew) ? $isNew : false;

$items = [];
$maps = ArrayHelper::getValue(Yii::$app->params, 'language.maps', ['en' => 'English']);
foreach (LanguageCode::options() as $key => $name) {
    $items[$key] = ['label' => $name, 'url' => Url::current(['language' => $key])];
}

if ($isNew) {
    echo $content;
    return;
}


if (isset($items[$current])) {
    $items[$current]['active'] = true;
    $items[$current]['url'] = '#';
}

?>
<div class="row">
    <div class="col-7 col-sm-8 col-md-10">
        <div class="tab-content">
            <div class="tab-pane active">
                <?= $content ?>
            </div>
        </div>
    </div>
    <div class="col-5 col-sm-4 col-md-2">
        <?= Tabs::widget([
            'navType' => 'flex-column nav-tabs nav-tabs-right h-100',
            'renderTabContent' => false,
            'items' => $items,
        ]); ?>
    </div>
</div>
