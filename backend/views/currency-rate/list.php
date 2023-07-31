<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\CurrencyRateSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\grid\GridView;
use common\base\helpers\IntegerDecimal;
use common\models\CurrencyRate;
use yii\helpers\Html;

/** @var \backend\controllers\CurrencyRateController $controller */
$controller = $this->context;

?>

<div class="card mb-0 h-100">
    <div class="card-header">
        <div class="card-tools">
            <?=  Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['/currency-rate/list'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
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
                'attribute' => 'sourceCurrencyCode',
                'value' => function (CurrencyRate $model) {
                    return sprintf('%s [%s]', $model->sourceCurrency->name, $model->sourceCurrency->code);
                }
            ],
            [
                'attribute' => 'targetCurrencyCode',
                'value' => function (CurrencyRate $model) {
                    return sprintf('%s [%s]', $model->targetCurrency->name, $model->targetCurrency->code);
                }
            ],
            [
                'attribute' => 'conversionRate',
                'value' => function (CurrencyRate $model) {
                    return number_format($model->conversionRateValue, 2);
                },
                'headerOptions' => ['style' => 'width: 150px']
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
