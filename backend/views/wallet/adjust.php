<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \backend\forms\AdjustWalletForm
 * @var $this \backend\base\web\View
 */

use backend\controllers\UserController;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\WalletController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0 h-100">
    <?php $form = ActiveForm::begin(['id' => 'wallet-adjust-form', 'layout' => 'horizontal', 'options' => ['autocomplete' => 'off']]) ?>
    <?php if ($model->hasErrors()) : ?>
        <div class="row">
            <div class="col-12">
                <?= $form->errorSummary($model) ?>
            </div>
        </div>
    <?php endif ?>

    <?= $form->field($model, 'amount')->textInput() ?>
    <?= $form->field($model, 'remark')->textarea(['row' => 3])?>

    <div class="row">
        <div class="col-sm-12 text-right">
            <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('view', ['view', 'id' => $model->wallet->id]), ['class' => 'btn']) ?>
            <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>

