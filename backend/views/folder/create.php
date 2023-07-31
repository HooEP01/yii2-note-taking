<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Folder $model */

$this->title = 'Create Folder';
$this->params['breadcrumbs'][] = ['label' => 'Folders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="folder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
