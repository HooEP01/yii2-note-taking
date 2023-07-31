<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Faq
 * @var $this \backend\base\web\View
 */

use common\base\enum\EditMode;
use common\base\helpers\Url;
use common\widgets\SimpleRichTextEditor;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\FaqController $controller */
$controller = $this->context;

$this->title = $controller->getName();
$this->params['breadcrumbs'][] = ['label' => $controller->getName(), 'url' => ['list']];
$this->params['breadcrumbs'][] = $model->getIsNewRecord() ? Yii::t('backend', 'breadcrumb.create') : Yii::t('backend', 'breadcrumb.update');

if ($mode === EditMode::PREVIEW) {
    $button =  Html::a(EditMode::resolve(EditMode::EDIT), Url::current(['mode' => EditMode::EDIT, '#' => 'faq-htmlAnswer']), [
        'class' => 'btn btn-xs btn-primary',
    ]);
} else {
    $button = Html::a(EditMode::resolve(EditMode::PREVIEW), Url::current(['mode' => EditMode::PREVIEW, '#' => 'faq-htmlAnswer']), [
        'class' => 'btn btn-xs btn-danger',
        'onclick' => new \yii\web\JsExpression(sprintf('return confirm(\'%s\')', Yii::t('backend', 'page_content.edit.cancel_confirmation'))),
    ]);
}

$mode = isset($mode) ? $mode : EditMode::EDIT;

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <?php $form = ActiveForm::begin(['id' => 'blog-form', 'options' => ['autocomplete' => 'off']]) ?>
                <?php if ($model->hasErrors()) : ?>
                    <div class="row">
                        <div class="col-12">
                            <?= $form->errorSummary($model) ?>
                        </div>
                    </div>
                <?php endif ?>

                <div class="row">
                    <div class="col col-11">
                        <?= $form->field($model, 'question') ?>
                    </div>
                    <div class="col">
                        <?= $form->field($model, 'position')->textInput(['type' => 'number']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <?php if (!$model->getIsNewRecord()) : ?>
                            <span class="float-right"><?= $button ?></span>
                        <?php endif; ?>
                        <?php if ($mode === EditMode::PREVIEW) : ?>
                            <div id="faq-htmlAnswer" class="mb-5">
                                <label><?= $model->getAttributeLabel('purifiedAnswer')?></label>
                                <hr class="mt-0" />
                                <section class="cms-content-preview">
                                    <?= empty($model->purifiedAnswer) ? '<p class="text-success">' . Yii::t('backend', 'form.text_editor.no_content') . '</p>' : $model->purifiedAnswer ?>
                                </section>
                                <p class="text-xs text-danger"><?= Yii::t('backend', 'form.text_editor.content_is_filtered') ?></p>
                            </div>
                        <?php else : ?>
                            <?= $form->field($model, 'htmlAnswer')
                                ->hint('<span class="text-danger">' . Yii::t('backend', 'form.text_editor.content_is_filtered') . '</span>')
                                ->widget(SimpleRichTextEditor::class, [
                                    'type' => SimpleRichTextEditor::TYPE_ADVANCE,
                                ]) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-right">
                        <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/blog/list']), ['class' => 'btn']) ?>
                        <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

