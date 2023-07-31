<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Address
 * @var $this \backend\base\web\View
 */

use common\models\City;
use common\models\Country;
use common\models\State;
use common\widgets\SelectDropdown;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

$user = $model->getOwnerModel();

?>

<div class="card card-body mb-0">
<?php $form = ActiveForm::begin(['id' => 'user-address-form', 'layout' => 'horizontal', 'options' => ['autocomplete' => 'off']]) ?>
<?php if ($model->hasErrors()) : ?>
    <div class="row">
        <div class="col-12">
            <?= $form->errorSummary($model) ?>
        </div>
    </div>
<?php endif ?>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'receiverName') ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'companyName') ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'address')->label(Yii::t('model', 'address.street_address'))->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'postcode', [
            'horizontalCssClasses' => [
                'label' => 'col-4',
                'wrapper' => 'col'
            ]
        ]) ?>
    </div>
    <div class="col">
        <?= $form->field($model, 'cityId', [
            'horizontalCssClasses' => [
                'label' => 'col-4',
                'wrapper' => 'col'
            ]
        ])->widget(SelectDropdown::class, ['data' => City::options()]) ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'stateCode', [
            'horizontalCssClasses' => [
                'label' => 'col-4',
                'wrapper' => 'col'
            ]
        ])->widget(SelectDropdown::class, ['data' => State::options()])?>
    </div>
    <div class="col">
        <?= $form->field($model, 'countryCode', [
            'horizontalCssClasses' => [
                'label' => 'col-4',
                'wrapper' => 'col'
            ]
        ])->widget(SelectDropdown::class, ['data' => Country::options()])?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'phoneNumber') ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-right">
        <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('user-address', ['address', 'id' => $user->id]), ['class' => 'btn']) ?>
        <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
    </div>
</div>
<?php ActiveForm::end() ?>
</div>

