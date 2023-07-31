<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\CurrencyRate
 * @var $this \backend\base\web\View
 */

use common\models\Currency;
use common\widgets\SelectDropdown;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\CurrencyRateController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0 h-100">
    <div class="row">
        <div class="col-12">
            <?php $form = ActiveForm::begin(['id' => 'currency-rate-form', 'options' => ['autocomplete' => 'off']]) ?>
            <?php if ($model->hasErrors()) : ?>
                <div class="row">
                    <div class="col-12">
                        <?= $form->errorSummary($model) ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'sourceCurrencyCode')->widget(SelectDropdown::class, ['data' => Currency::options()]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'targetCurrencyCode')->widget(SelectDropdown::class, ['data' => Currency::options()]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'conversionRateValue') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/currency-rate/list']), ['class' => 'btn']) ?>
                    <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>

