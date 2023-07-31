<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\City
 * @var $this \backend\base\web\View
 */

use common\models\State;
use common\widgets\SelectDropdown;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\CityController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

?>

<div class="card card-body mb-0">
    <?php $this->beginContent('@app/views/_common/_language_tab.php', ['isNew' => $model->getIsNewRecord()]); ?>
    <div class="row">
        <div class="col-12">
            <?php $form = ActiveForm::begin(['id' => 'city-form', 'options' => ['autocomplete' => 'off']]) ?>
            <?php if ($model->hasErrors()) : ?>
                <div class="row">
                    <div class="col">
                        <?= $form->errorSummary($model) ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col col-md-10">
                    <?= $form->field($model, 'stateCode')->widget(SelectDropdown::class, ['data' => State::options()]) ?>
                </div>
                <div class="col col-md-2">
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
                    <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['list']), ['class' => 'btn']) ?>
                    <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <?php $this->endContent() ?>

</div>

