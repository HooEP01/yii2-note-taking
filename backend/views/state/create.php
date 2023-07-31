<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\State
 * @var $this \backend\base\web\View
 */

/** @var \backend\controllers\StateController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = $controller->getName();

?>

<div class="card">
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model])?>
    </div>
</div>


