<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\Language
 * @var $this \backend\base\web\View
 */

/** @var \backend\controllers\LanguageController $controller */
$controller = $this->context;

echo $this->render('_form', ['model' => $model]);


