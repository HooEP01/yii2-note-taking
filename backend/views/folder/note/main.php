<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $account \common\models\Account
 * @var $model \common\models\folder
 * @var $content string
 * @var $this \backend\base\web\View
 */

use common\base\helpers\ArrayHelper;
use common\widgets\Tabs;

$current = isset($current) ? $current : $this->getActionName();

$items = [
    'note' => ['label' => Yii::t('backend', 'breadcrumb.list'), 'url' => ['/folder/note', 'id' => $folder->id]],
    'note-create' => ['label' =>  Yii::t('backend', 'folder.tab.note_create'), 'url' => '#', 'visible' => ($current === 'note-create')],
];

if (isset($model)) {
    $items['note-update'] = ['label' => Yii::t('backend', 'folder.tab.note_update'), 'url' => '#', 'visible' => ($current === 'note-update')];
    $items['note-view'] = ['label' => Yii::t('backend', 'folder.tab.note_view'), 'url' => '#', 'visible' => ($current === 'note-view')];
}

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

echo Tabs::widget([
    'items' => $items
]);
