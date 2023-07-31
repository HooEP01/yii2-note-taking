<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\UserSocial
 * @var $this \backend\base\web\View
 */

use common\base\enum\SocialChannel;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0 h-100">
<?php $form = ActiveForm::begin(['id' => 'user-social-form', 'options' => ['autocomplete' => 'off']]) ?>
<?php if ($model->hasErrors()) : ?>
    <div class="row">
        <div class="col-12">
            <?= $form->errorSummary($model) ?>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'channel')->dropDownList(SocialChannel::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
    </div>
    <div class="col-sm-9">
        <?= $form->field($model, 'channelId') ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'channelName') ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'isVerified')->checkbox() ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-right">
        <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('user-social', ['social', 'id' => $model->userId]), ['class' => 'btn']) ?>
        <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
    </div>
</div>
<?php ActiveForm::end() ?>
</div>