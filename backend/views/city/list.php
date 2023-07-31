<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\CitySearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\grid\GridView;
use common\base\helpers\Popup;
use common\models\City;
use yii\helpers\Html;

/** @var \backend\controllers\CityController $controller */
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
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'name'
            ],
            [
                'class' => 'common\base\grid\TranslateColumn',
                'attribute' => 'shortName'
            ],
            [
                'attribute' => 'countryCode',
                'format' => 'raw',
                'value' => function (City $city) {
                    if (!$city->country) {
                        return $city->countryCode;
                    }

                    return Popup::factory()
                        ->text($city->country->name)
                        ->url(['/country/update', 'code' => $city->country->code]);
                },
                'headerOptions' => ['style' => 'width: 120px;']
            ],
            [
                'attribute' => 'stateCode',
                'format' => 'raw',
                'value' => function (City $city) {
                    if (!$city->state) {
                        return $city->stateCode;
                    }

                    return Popup::factory()
                        ->text($city->state->name)
                        ->url(['/state/update', 'code' => $city->state->code]);
                },
                'headerOptions' => ['style' => 'width: 120px;']
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
            ],
        ],
    ]); ?>
</div>


