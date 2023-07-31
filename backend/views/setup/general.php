<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $setup \backend\setups\GeneralSetup
 * @var $this \backend\base\web\View
 */

use common\base\enum\OtpProvider;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'system_setting.general');

?>

<div class="card card-outline h-100">
    <div class="card-body">
        <?php $form = ActiveForm::begin(['id' => 'general-setup-form', 'options' => ['autocomplete' => 'off']]) ?>
        <?php if ($setup->hasErrors()) : ?>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->errorSummary($setup) ?>
                </div>
            </div>
        <?php endif ?>

        <div class="row">
            <div class="col">
                <?= $form->field($setup, 'facebookAppId')?>
                <?= $form->field($setup, 'facebookAppSecret')?>
                <?= $form->field($setup, 'googleMapApiKey')->hint('* required if using Google Map API, e.g. Javascript google.maps.Map()') ?>

            </div>
            <div class="col">
                <?= $form->field($setup, 'otpProvider')->dropdownList(OtpProvider::options()) ?>

                <fieldset>
                    <legend>Security Settings</legend>
                    <?= $form->field($setup, 'enforceSuperAdminMultiFactorAuthentication')->checkbox() ?>
                </fieldset>


            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 text-right">
                <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>



