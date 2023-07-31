<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\User
 * @var $this \backend\base\web\View
 */


?>

<div class="card card-light card-outline mb-0">
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model])?>

        <?php if ($this->user->getIsSystemAdmin()): ?>
            <hr style="margin: 10px 0">
            <span class="text-danger">** System Admin Section **</span>
            <div class="system-admin-zone">
                <strong>Access Token: </strong>
                <?php $token = Yii::$app->jwt->issueToken(['sub' => $model->id, 'jti' => $model->authKey]) ?>
                <?= $token->toString() ?>
            </div>
        <?php endif ?>
    </div>
</div>
