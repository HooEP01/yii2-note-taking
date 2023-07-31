<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\User
 * @var $this \backend\base\web\View
 */

use backend\controllers\UserController;

/** @var UserController $controller */
$controller = $this->context;

$this->title =  Yii::t('backend', 'breadcrumb.user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'breadcrumb.user'), 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.create');

?>

<div class="card card-light card-outline">
    <div class="card-header">
        <h5>General Information</h5>
    </div>
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model])?>
    </div>
</div>
