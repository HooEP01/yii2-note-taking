<?php

use common\base\enum\NotePriorityType;
use common\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Note $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="note-form p-2">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'priority')->dropDownList(NotePriorityType::options()) ?>

    <?= $form->field($model, 'due_date')->widget(DatePicker::class, [
        'pluginOptions' => [
            'multidate' => false,
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>