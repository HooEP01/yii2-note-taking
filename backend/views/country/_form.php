<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Country
 * @var $this \backend\base\web\View
 */

use common\base\enum\CurrencyCode;
use common\models\State;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\CountryController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

?>

<?php if (!$model->getIsNewRecord()) : ?>
<div class="row">
    <div class="col-3 pr-0 d-flex align-items-stretch">
        <?= $this->render('_tabs', ['model' => $model])?>
    </div>
    <div class="col-9 pl-0">
<?php endif; ?>
<div class="card card-body mb-0">
    <?php $this->beginContent('@app/views/_common/_language_tab.php', ['isNew' => $model->getIsNewRecord()]); ?>
    <div class="row">
        <div class="col">
            <?php $form = ActiveForm::begin(['id' => 'country-form', 'options' => ['autocomplete' => 'off']]) ?>
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
                <div class="col">
                    <?= $form->field($model, 'iso3') ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'numCode') ?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'telCode') ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'currencyCode')->dropDownList(CurrencyCode::options(), ['prompt' => Yii::t('backend', 'form.dropdown.select_one')])?>
                </div>
                <div class="col">
                    <?= $form->field($model, 'defaultStateCode')
                        ->widget(\common\widgets\SelectDropdown::class, ['data' => State::options()])?>
                </div>
                <div class="col"></div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'isStateRequired')->checkbox() ?>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <?= $form->field($model, 'isPostcodeRequired')->checkbox() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/country/list']), ['class' => 'btn']) ?>
                    <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <?php $this->endContent() ?>
</div>
<?php if (!$model->getIsNewRecord()) : ?>
    </div>
</div>
<?php endif; ?>
