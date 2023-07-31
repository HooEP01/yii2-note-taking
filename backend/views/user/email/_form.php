<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\UserEmail
 * @var $this \backend\base\web\View
 */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

?>
<div class="card card-body mb-0 h-100">
<?php $form = ActiveForm::begin(['id' => 'user-email-form', 'options' => ['autocomplete' => 'off']]) ?>
<?php if ($model->hasErrors()) : ?>
    <div class="row">
        <div class="col-12">
            <?= $form->errorSummary($model) ?>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'address') ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'isVerified')->checkbox() ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-right">
        <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('user-email', ['email', 'id' => $model->userId]), ['class' => 'btn']) ?>
        <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
    </div>
</div>
<?php ActiveForm::end() ?>
</div>