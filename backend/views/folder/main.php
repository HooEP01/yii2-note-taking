<?php

/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\folder
 * @var $content string
 * @var $this \backend\base\web\View
 */

use backend\controllers\FolderController;
use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

/** @var FolderController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.folder');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'breadcrumb.folder'), 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.update');

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'view' => ['label' => Yii::t('backend', 'common.tab.basic'), 'url' => ['/folder/view', 'id' => $model->id]],
    'note' => ['label' => Yii::t('backend', 'folder.tab.note'), 'url' => ['/folder/note', 'id' => $model->id]],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

$tab = $current;
if (($pos = strpos($current, '-')) !== false) {
    $tab = substr($current, 0, $pos);
}

if (in_array($controller->id, ['wallet'])) {
    $tab = $controller->id;
}

if (in_array($tab, ['wallet', 'note'])) {
    ArrayHelper::remove($items[$tab], 'url');
    $items[$tab]['active'] = true;
    $items[$tab]['content'] = $content;
} 

?>

<div class="row">
    <div class="col-md-12">
        <?= Tabs::widget(['items' => $items]); ?>
    </div>
</div>