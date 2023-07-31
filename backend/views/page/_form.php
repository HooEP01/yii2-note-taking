<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\PageContent
 * @var $this \backend\base\web\View
 */

use common\base\enum\EditMode;
use common\base\helpers\Url;
use common\widgets\SimpleRichTextEditor;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\PageController $controller */
$controller = $this->context;

$this->title = $controller->getName();
$this->params['breadcrumbs'][] = ['label' => $controller->getName(), 'url' => ['list']];
$this->params['breadcrumbs'][] = $model->getIsNewRecord() ? Yii::t('backend', 'breadcrumb.create') : Yii::t('backend', 'breadcrumb.update');

$mode = isset($mode) ? $mode : EditMode::EDIT;

?>

<?php $this->beginContent('@app/views/_common/_language_tab.php', ['isNew' => $model->getIsNewRecord()]); ?>
<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(['id' => 'page-content-form', 'options' => ['autocomplete' => 'off']]); ?>

        <?php if ($model->hasErrors()) : ?>
            <div class="row">
                <div class="col-12">
                    <?= $form->errorSummary($model) ?>
                </div>
            </div>
        <?php endif ?>

        <div class="row">
            <div class="col-sm-10">
                <?= $form->field($model, 'code')->textInput(['autofocus' => true]); ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'position')->input('number'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'name')->textInput(); ?>
                <?= $form->field($model, 'title')->textInput(); ?>
            </div>
        </div>

        <?php if ($mode === EditMode::PREVIEW) : ?>
            <?= Html::a('Edit', Url::current(['mode' => EditMode::EDIT]), ['class' => 'btn btn-xs btn-primary float-right']) ?>

            <p class="section-title">
                <?= sprintf('%s - %s Mode', $model->getAttributeLabel('htmlContent'), ucfirst($mode)) ?>
            </p>

            <small class="text-danger">
                <?= Yii::t('backend', 'form.text_editor.content_is_filtered') ?>
            </small>

            <br />

            <section class="cms-content-preview">
                <?= empty($model->purifiedContent) ? '<small class="text-success">' . Yii::t('backend', 'form.text_editor.no_content') . '</small>' : $model->purifiedContent ?>
            </section>
        <?php else : ?>
            <?php if (!$model->getIsNewRecord()) : ?>
                <div class="float-right">
                    <?= Html::a(Yii::t('backend', 'action.cancel'), Url::current(['mode' => EditMode::PREVIEW]), [
                        'class' => 'btn btn-xs btn-danger',
                        'onclick' => new \yii\web\JsExpression(sprintf('return confirm(\'%s\')', Yii::t('backend', 'page_content.edit.cancel_confirmation'))),
                    ]) ?>
                </div>
            <?php endif; ?>

            <p class="section-title"><?= sprintf('%s - %s Mode', $model->getAttributeLabel('htmlContent'), ucfirst($mode)) ?></p>

            <?= $form->field($model, 'htmlContent')->label(false)->widget(SimpleRichTextEditor::class); ?>
        <?php endif ?>
        <div class="row">
            <div class="col-sm-12 text-right">
                <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getDefaultListUrl(), ['class' => 'btn']) ?>
                <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php $this->endContent() ?>