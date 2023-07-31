<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \backend\forms\LoginForm
 * @var $this View
 */

use backend\base\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="login-box">
    <div class="login-logo">
        <?= Html::img($this->getBaseUrl() . '/images/logos/logo_horizontal_default.png', ['style' => 'width: 100%; max-width: 200px;']) ?>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?php $icon = '<div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>'; ?>
            <?= $form->field($model, 'username', [
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'template' => "{label}\n{beginWrapper}\n{input}\n{$icon}\n{error}\n{hint}\n{endWrapper}"
            ])->label(false)
                ->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('username')]) ?>

            <?php $icon = ' <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>' ?>
            <?= $form->field($model, 'password', [
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'template' => "{label}\n{beginWrapper}\n{input}\n{$icon}\n{error}\n{hint}\n{endWrapper}"
            ])->label(false)
                ->passwordInput(['placeholder' =>  $model->getAttributeLabel('password')]) ?>
            <div class="row">
                <div class="col-8">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <p class="mt-3 mb-1"><?= Html::a('I forgot my password', '#') ?></p>
        </div>
    </div>

    <p class="text-center" style="font-family: Nunito, sans-serif; font-size: 12px;">Super Admin Portal <?= Yii::$app->version ?></p>
</div>