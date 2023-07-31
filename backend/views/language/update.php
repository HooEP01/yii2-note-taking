<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Language
 * @var $this \backend\base\web\View
 */

/** @var \backend\controllers\LanguageController $controller */
$controller = $this->context;

?>

<div class="row h-100">
    <div class="col-2 pr-0 d-flex align-items-stretch">
        <?= $this->render('_tabs', ['model' => $model])?>
    </div>
    <div class="col-10 pl-0">
        <?= $this->render('_form', ['model' => $model])?>
    </div>
</div>


