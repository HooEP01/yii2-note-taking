<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \backend\forms\ResetPasswordForm
 * @var $this \backend\base\web\View
 */


use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

?>
<div class="card card-body mb-0 h-100">
    <?php $form = ActiveForm::begin(['id' => 'user-form']) ?>
    <?php if ($model->getIsAllowed()): ?>
        <?php if ($model->hasErrors()) : ?>
            <div class="row">
                <div class="col-12">
                    <?= $form->errorSummary($model) ?>
                </div>
            </div>
        <?php endif ?>

        <?= $form->field($model, 'new_password')->passwordInput() ?>

        <?= $form->field($model, 'confirm_password')->passwordInput() ?>

        <div class="row">
            <div class="col-sm-12 text-right">
                <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['list', 'id' => $model->user->id]), ['class' => 'btn']) ?>
                <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
            </div>
        </div>
    <?php else: ?>
        <div class="callout callout-danger mb-0">
            <p><?= Yii::t('backend', 'user.password.not_allowed') ?></p>
        </div>
    <?php endif; ?>
    <?php ActiveForm::end() ?>
</div>