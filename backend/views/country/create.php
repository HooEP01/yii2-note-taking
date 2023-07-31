<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

/** @var \backend\controllers\CountryController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

echo $this->render('_form', ['model' => $model]);

