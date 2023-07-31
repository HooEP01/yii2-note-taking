<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Currency
 * @var $this \backend\base\web\View
 */

use common\base\enum\CurrencyFormat;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\CurrencyController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0">
    <?php $this->beginContent('@app/views/_common/_language_tab.php', ['isNew' => $model->getIsNewRecord()]); ?>
    <div class="row">
        <div class="col-12">
            <?php $form = ActiveForm::begin(['id' => 'currency-form', 'options' => ['autocomplete' => 'off']]) ?>
            <?php if ($model->hasErrors()) : ?>
                <div class="row">
                    <div class="col-12">
                        <?= $form->errorSummary($model) ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col-9">
                    <?= $form->field($model, 'code') ?>
                </div>
                <div class="col-3">
                    <?= $form->field($model, 'position') ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'name') ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'shortName') ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'symbol') ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'format')->dropDownList(CurrencyFormat::options()) ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'precision') ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'decimalPoint') ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'thousandsSeparator') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/currency/list']), ['class' => 'btn']) ?>
                    <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <?php $this->endContent() ?>

</div>
