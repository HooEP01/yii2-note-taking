<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\User
 * @var $this \backend\base\web\View
 */

use common\base\enum\Gender;
use common\base\enum\NameFormat;
use common\base\enum\UserRole;
use common\base\enum\UserStatus;
use common\models\Currency;
use common\models\Language;
use common\widgets\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

?>

<?php $form = ActiveForm::begin(['id' => 'user-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => '',
                'wrapper' => 'col-sm-8',
            ],
        ],
        'options' => ['autocomplete' => 'off']]
) ?>
<?php if ($model->hasErrors()) : ?>
    <div class="row">
        <div class="col-12">
            <?= $form->errorSummary($model) ?>
        </div>
    </div>
<?php endif ?>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'status')->dropDownList(UserStatus::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')])?>
        </div>
        <div class="col-12">
            <?= $form->field($model, 'username')->hint(Yii::t('backend', 'user.username.hint')) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'firstName', [
                'horizontalCssClasses' => [
                    'label' => 'col-6',
                    'wrapper' => 'col'
                ]
            ])->label('Name')->textInput(['placeholder' => $model->getAttributeLabel('firstName')]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'lastName', [
                'horizontalCssClasses' => [
                    'offset' => '',
                    'wrapper' => 'col-12'
                ]
            ])->label(false)->textInput(['placeholder' => $model->getAttributeLabel('lastName')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'nameFormat')->dropDownList(NameFormat::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
        </div>
        <div class="col-12">
            <?= $form->field($model, 'displayName') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'gender')->dropDownList(Gender::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
        </div>
        <div class="col-12">
            <?= $form->field($model, 'dateOfBirth')->widget(DatePicker::class) ?>
        </div>
        <div class="col-12">
            <?= $form->field($model, 'roleItems')->dropDownList(UserRole::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'languageCode')->dropDownList(Language::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
            <?= $form->field($model, 'currencyCode')->dropDownList(Currency::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-right">
            <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['list', 'id' => $model->id]), ['class' => 'btn']) ?>
            <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
        </div>
    </div>
<?php ActiveForm::end() ?>