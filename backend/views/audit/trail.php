<?php

/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.alpstein.my
 * @copyright Copyright (c) Alpstein Software
 *
 * @var $this \backend\base\web\View
 * @var $searchModel backend\models\AuditTrailSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use backend\models\AuditTrailSearch;
use common\base\audit\models\AuditTrail;
use common\base\grid\GridView;
use common\base\helpers\Json;
use yii\helpers\Html;

$this->title = 'Database Records Change Logs';
$this->params['breadcrumbs'][] = 'Audit';
$this->params['breadcrumbs'][] = 'Trail';

?>

<div class="card card-outline card-navy">
    <div class="card-header">
        <h5 class="card-title"><?= $this->title ?></h5>

        <div class="card-tools">
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['trail'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'yii-grid card-body p-0'],
        'columns' => [
            ['attribute' => 'id', 'headerOptions' => ['style' => 'width: 50px;'], 'contentOptions' => ['style' => 'white-space: normal;']],
            [
                'attribute' => 'userId',
                'value' => function (AuditTrail $model) {
                    $user = $model->getUser()->one();
                    return $user ? $user->name : null;
                },
                'label' => 'User',
                'headerOptions' => ['style' => 'width: 100px;'],
                'format' => 'raw',
            ],
            ['attribute' => 'action', 'headerOptions' => ['style' => 'width: 60px;'], 'filter' => AuditTrailSearch::actionFilter()],
            [
                'attribute' => 'modelClass',
                'label' => 'Model (M)',
                'value' => function (AuditTrail $model) {
                    return substr($model->modelClass, strrpos($model->modelClass, '\\') + 1);
                },
                'headerOptions' => ['style' => 'width: 80px;'],
            ],
            ['attribute' => 'modelKey', 'label' => 'M.ID', 'headerOptions' => ['style' => 'width: 80px;'], 'contentOptions' => ['style' => 'white-space: normal;']],
            ['attribute' => 'field', 'headerOptions' => ['style' => 'width: 100px;']],
            [
                'label' => 'Different',
                'value' => function (AuditTrail $model) {
                    return $model->getDiffHtml();
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'white-space: normal;']
            ],
            ['attribute' => 'createdAt', 'headerOptions' => ['style' => 'width: 100px;']],
        ],
    ]); ?>
</div>