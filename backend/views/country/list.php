<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\CountrySearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 *
 */

use common\base\grid\GridView;
use common\models\Country;
use yii\helpers\Html;

/** @var \backend\controllers\CountryController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

?>

<div class="card h-100">
    <div class="card-header">
        <div class="card-tools">
            <?=  Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
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
                'template' => '{update} {image}',
                'buttons' => [
                    'update' => function ($url, Country $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'far fa-edit']);
                        return Html::a($icon, ['update', 'code' => $model->code], ['title' => 'Update', 'class' => 'grid-link']);
                    },
                    'image' =>  function ($url, Country $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'far fa-image']);
                        return Html::a($icon, ['image', 'code' => $model->code], ['title' => 'Logo', 'class' => 'grid-link']);
                    },
                ],
                'dropdownItems' => [
                    'delete' => function ($url, Country $model, $key) {
                        return [
                            'label' => $model->getIsActive() ? '<i class="far fa-trash-alt mr-3"></i>Delete' : '<i class="fas fa-undo mr-3"></i>Restore',
                            'url' => ['toggle', 'code' => $model->code],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                            'linkOptions' => ['data-method' => 'post', 'data-confirm' => $model->getIsActive() ? Yii::t('backend', 'model.delete.confirmation') : Yii::t('backend', 'model.restore.confirmation')]
                        ];
                    },
                ],
            ],
        ],
    ]); ?>
</div>
