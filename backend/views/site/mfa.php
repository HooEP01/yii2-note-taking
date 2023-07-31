<?php

/**
 * @author RYU Chua <ryu@riipay.my>
 *  @link https://riipay.my
 * @copyright Copyright (c) Riipay
 *
 * @var $this View
 * @var $form ActiveForm
 * @var $model MfaLoginForm
 */

use backend\base\web\View;
use backend\forms\MfaLoginForm;
use common\base\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$params = Yii::$app->params;
$logoImage = ArrayHelper::getValue($params, 'backend.logo.image');
$vendorName = ArrayHelper::getValue($params, 'vendor.name');

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="login-box">
    <div class="login-logo">
        <?= Html::img($this->getBaseUrl() . '/images/logos/logo_horizontal_default.png', ['style' => 'width: 100%; max-width: 200px;']) ?>
    </div>

    <div class="card card-body">
        <div class="login-box-body">
            <p class="login-box-msg">Enter Your OTP</p>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= Html::activeHiddenInput($model, 'username') ?>
            <?= Html::activeHiddenInput($model, 'password') ?>
            <?= Html::activeHiddenInput($model, 'rememberMe') ?>

            <?= $form->field($model, 'otp', ['options' => ['class' => 'form-group has-feedback', 'autocomplete' => 'off'], 'inputTemplate' => '{input}<span class="glyphicon glyphicon-equalizer form-control-feedback"></span>'])
                ->label(false)->textInput(['autofocus' => true, 'placeholder' => 'OTP']) ?>

            <div class="row">
                <div class="col-xs-8">
                    <?= Html::a('cancel', ['login'], ['class' => 'btn']) ?>
                </div>
                <div class="col-xs-4">
                    <input type="submit" class="btn btn-primary btn-block btn-flat" value="Submit" />
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>