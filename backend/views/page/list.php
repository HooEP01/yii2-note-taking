<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\PageContentSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\grid\GridView;
use yii\helpers\Html;

/** @var \backend\controllers\PageController $controller */
$controller = $this->context;

$this->title = $controller->getName();
$this->params['breadcrumbs'][] = $controller->getName();
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.list');

?>

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <?= Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['list'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'yii-grid card-body p-0'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width: 25px;']
            ],
            [
                'attribute' => 'code',
                'headerOptions' => ['style' => 'width:150px'],
            ],
            [
                'attribute' => 'slug',
                'headerOptions' => ['style' => 'width:150px'],
            ],
            [
                'attribute' => 'name'
            ],
            [
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'title'
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isActive',
                'visible' => $this->user->getIsSuperAdmin(),
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update} {toggle}',
            ],
        ],
    ]); ?>
</div>

