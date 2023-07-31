<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\CurrencySearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\grid\GridView;
use common\models\Currency;
use yii\helpers\Html;

/** @var \backend\controllers\CurrencyController $controller */
$controller = $this->context;

?>

<div class="card mb-0">
    <div class="card-header">
        <div class="card-tools">
            <?=  Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['/currency/list'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
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
                'headerOptions' => ['style' => 'width: 100px']
            ],
            [
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'name'
            ],
            [
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'shortName'
            ],
            [
                'attribute' => 'position',
                'headerOptions' => ['style' => 'width: 35px;']
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isActive',
                'visible' => $this->user->getIsSuperAdmin(),
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update} {toggle}',
                'buttons' => [
                    'update' => function ($url, Currency $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'far fa-edit']);
                        return Html::a($icon, ['update', 'code' => $model->code], ['title' => 'Update', 'class' => 'grid-link']);
                    },
                    'toggle' => function ($url, Currency $model, $key) {
                        if ($model->getIsActive()) {
                            $icon = Html::tag('i', '', ['class' => 'far fa-trash-alt']);
                            return Html::a($icon, ['toggle', 'code' => $model->code], ['title' => 'Delete', 'class' => 'grid-link', 'data-method' => 'post', 'data-confirm' => Yii::t('backend', 'model.delete.confirmation')]);
                        }

                        $icon = Html::tag('i', '', ['class' => 'fas fa-undo']);
                        return Html::a($icon, ['toggle', 'code' => $model->code], ['title' => 'Restore', 'class' => 'grid-link', 'data-method' => 'post', 'data-confirm' => Yii::t('backend', 'model.restore.confirmation')]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
