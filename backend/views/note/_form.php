<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use common\base\helpers\Url;
use common\widgets\SelectDropdown;
use common\models\Folder;
use common\base\helpers\Json;
use kartik\switchinput\SwitchInput;
use common\base\DateTime;

/** @var yii\web\View $this */
/** @var common\models\Note $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php $folderOptions = [['id' => 'r2i3grih2r', 'text' => 'efwoiofeoij']]; ?>
       
            <?= $form->field($model, 'folder_id')
                ->widget(SelectDropdown::class, [
                    'data' => $folderOptions,
                    'placeholder' => 'Enter name to search',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => Url::to(['/folder/ajax-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(folder) { return folder.text; }'),
                        'templateSelection' => new JsExpression('function (folder) { return folder.text; }'),
                    ]
                ])
                ->hint(Yii::t('backend', 'voucher.folder_id.hint'))
                ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'due_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
