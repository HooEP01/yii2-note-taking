<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Language
 * @var $this \backend\base\web\View
 */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\LanguageController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0 h-100">
    <?php $this->beginContent('@app/views/_common/_language_tab.php', ['isNew' => $model->getIsNewRecord()]); ?>
    <div class="row">
        <div class="col-12">
            <?php $form = ActiveForm::begin(['id' => 'language-form', 'options' => ['autocomplete' => 'off']]) ?>
            <?php if ($model->hasErrors()) : ?>
                <div class="row">
                    <div class="col-xs-12">
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
                <div class="col-sm-12 text-right">
                    <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/language/list']), ['class' => 'btn']) ?>
                    <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <?php $this->endContent() ?>
</div>

