<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model User
 * @var $this View
 */

use backend\base\web\View;
use common\models\User;
use yii\helpers\Html;

?>
<div class="card card-body mb-0 h-100">
    <?php if (!empty($model->authMfaToken)): ?>
        <div class="row">
            <div class="col-sm-4">
                <?php $otp = $model->getTimeBaseOTP() ?>
                <?php $uri = $otp->getProvisioningUri(); ?>
                <?php $src = Yii::$app->qr
                    ->useLogo()
                    ->setText($uri)
                    ->setLogoWidth(100)
                    ->setSize(500)
                    ->writeDataUri(); ?>
                <?= Html::img($src, ['style' => 'width: 100%;']) ?>
            </div>
            <div class="col-sm-8">
                <p>Scan the QR code with you authenticator APP</p>
                <p>
                    To Reset, click: <br />
                    <?= Html::a('Reset MFA', ['mfa', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm', 'data-method' => 'post']) ?>
                </p>

                <p>
                    To Disabled, click: <br />
                    <?= Html::a('Deactivate MFA', ['mfa', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'delete']) ?>
                    <br />
                    <span class="text-danger">** SuperAdmin are not allowed to disabled this ! you will not be able to login !</span>
                </p>

                <p>
                    Current Code: <br />
                    <span class="text-primary" style="font-size: 36px; letter-spacing: 2px; font-weight: bold"><?= $otp->now() ?></span><br />
                    <span class="text-danger">** Code will be keep changing, click refresh to see latest !</span> <br />
                    <?= Html::a('Refresh', ['mfa', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted">MFA - Multi-Factor Authentication is not activated !</p>
        <p>Click the button below to activate it !</p>
        <p><?= Html::a('Activate MFA', ['mfa', 'id' => $model->id], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post']) ?></p>
    <?php endif; ?>
</div>
