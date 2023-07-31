<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Language
 * @var $image \common\models\Image
 * @var $this \backend\base\web\View
 */

use common\widgets\ImageFileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/** @var \backend\controllers\LanguageController $controller */
$controller = $this->context;

?>

<div class="row h-100">
    <div class="col-2 pr-0 d-flex align-items-stretch">
        <?= $this->render('_tabs', ['model' => $model])?>
    </div>
    <div class="col-10 pl-0">
        <div class="card card-body mb-0 h-100">
            <div class="row">
                <div class="col-sm-6">
                    <p class="lead"><?= Yii::t('backend', 'form.label.current_image') ?></p>
                    <?php if ($image->getHasImage()) : ?>
                        <span class="badge badge-info"><?= Yii::$app->formatter->asShortSize($image->size) ?></span>
                        <span class="badge badge-success"><?= $image->width ?>px vs <?= $image->height ?>px</span>
                        <span class="badge badge-primary"><?= $image->format ?></span>
                        <hr />
                        <?= Html::img($image->getImageSrc(), ['class' => 'img-fluid', 'style' => 'max-width: 300px']) ?>
                    <?php else : ?>
                        <div class="callout callout-info">
                            <h4><?= Yii::t('backend', 'form.label.no_image') ?></h4>
                            <p><?= Yii::t('backend', 'form.label.please_upload_image') ?></p>
                        </div>
                    <?php endif ?>
                </div>
                <div class="col-sm-6">
                    <?php $form = ActiveForm::begin(['id' => 'language-image-form', 'options' => ['autocomplete' => 'off']]); ?>
                    <?php if ($image->hasErrors()) : ?>
                        <div class="row">
                            <div class="col-12">
                                <?= $form->errorSummary($image) ?>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php $hint = Yii::$app->config->getImageUploaderHint(); ?>
                            <?= $form->field($image, 'upload')
                                ->label(Yii::t('backend', 'form.label.upload_new_image'))
                                ->hint(nl2br($hint))
                                ->widget(ImageFileInput::class) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= Html::a(Yii::t('backend', 'form.button.cancel'), $controller->getRememberedUrl('list', ['/country/list']), ['class' => 'btn']) ?>
                            <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.upload') ?></button>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

