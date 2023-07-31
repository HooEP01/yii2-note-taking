<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Wallet
 * @var $transactionSearchModel \backend\models\WalletTransactionSearch
 * @var $transactionDataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\enum\CurrencyCode;
use common\base\grid\GridView;
use common\models\WalletTransaction;
use yii\helpers\Html;

?>

<div class="card card-body mb-0 h-100">
    <div class="row mb-5">
        <div class="col-sm-8">
            <div class="text-muted">
                <?= $model->getAttributeLabel('cacheBalance') ?>
                <span class="text-bold"><?= Yii::$app->formatter->asAccountingPrice($model->getBalanceIntegerDecimal(), $model->getPriceFormatOptions()) ?></span>
            </div>
        </div>
        <div class="col-sm-4 text-right">
            <?= Html::a(Yii::t('backend', 'form.button.adjust'), ['/wallet/adjust', 'id' => $model->id], [
                'class' => 'btn btn-warning btn-flat btn-xs mb-3',
            ]) ?>

            <?= Html::a(Yii::t('backend', 'form.button.recalculate'), ['/wallet/recalculate', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-flat btn-xs mb-3',
                'data-method' => 'post',
            ]) ?>
        </div>
    </div>

    <?= Html::a('<i class="fas fa-sync float-right"></i>', ['/wallet/view', 'id' => $model->id], ['encode' => false, 'class' => 'btn btn-tool']); ?>
    <h6><?= Yii::t('backend', 'common.tab.wallet_transaction') ?></h6>
    <div class="row">
        <div class="col">
            <?= GridView::widget([
                'dataProvider' => $transactionDataProvider,
                'filterModel' => $transactionSearchModel,
                'options' => ['class' => 'yii-grid'],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width: 25px;']
                    ],
                    [
                        'class' => 'common\base\grid\DatetimeColumn',
                        'attribute' => 'createdAt'
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function (WalletTransaction $transaction) {
                            return $transaction->getTypeName();
                        },
                        'headerOptions' => ['style' => 'width: 100px;']
                    ],
                    [
                        'attribute' => 'description',
                        'value' => function (WalletTransaction $transaction) {
                            return $transaction->getDescription();
                        }
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function (WalletTransaction $transaction) {
                            if ($transaction->wallet->currencyCode === CurrencyCode::PG_POINT) {
                                return Yii::$app->formatter->asPoint($transaction->getAmountIntegerDecimal(), $transaction->getPriceFormatOptions());
                            }
                            return Yii::$app->formatter->asAccountingPrice($transaction->getAmountIntegerDecimal(), $transaction->getPriceFormatOptions());
                        },
                        'headerOptions' => ['style' => 'width: 150px; text-align: right'],
                        'contentOptions' => ['style' => 'text-align: right'],
                    ],
                ]
            ]) ?>
        </div>
    </div>
</div>



