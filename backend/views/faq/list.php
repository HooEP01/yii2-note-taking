<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $active boolean
 * @var $searchModel \backend\models\FaqSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\grid\GridView;
use yii\bootstrap4\ButtonGroup;
use yii\helpers\Html;

/** @var \backend\controllers\FaqController $controller */
$controller = $this->context;

$this->title = $controller->getName();
$this->params['breadcrumbs'][] = $controller->getName();
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.list');

?>

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <?=  Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', $controller->action->id, ['encode' => false, 'class' => 'btn btn-tool']); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'yii-grid card-body p-0'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn', 'headerOptions' => ['style' => 'width: 25px;']
            ],
            [
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'question'
            ],
            [
                'attribute' => 'position',
                'headerOptions' => ['style' => 'width: 80px']
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update} {toggle}',
            ],
        ],
    ]); ?>
</div>
