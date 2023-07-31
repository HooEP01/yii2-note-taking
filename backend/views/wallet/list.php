<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $this \backend\base\web\View
 */

use backend\controllers\UserController;
use yii\helpers\Html;


/** @var UserController $controller */
$controller = $this->context;

?>

<div class="card card-body mb-0 h-100">
    <?php if (!empty($user->getWallets())) : ?>
        <h4>Total: <?= count($user->getWallets()) ?></h4>
        <div class="row">
            <?php foreach ($user->getWallets() as $wallet) : ?>
                <div class="col-sm-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title mb-3"><?= $wallet->currency->name ?></h4>
                            <p class="card-text text-muted mb-0">Current Balance</p>
                            <p class="card-text text-bold mt-0 mb-5" style="font-size: 30px;"><?= Yii::$app->formatter->asAccountingPrice($wallet->getBalance(), $wallet->getPriceFormatOptions()) ?></p>
                            <?= Html::a('<i class="fa fa-angle-right mr-1"></i>' . 'View More', ['/wallet/view', 'id' => $wallet->id], ['class' => 'text-muted stretched-link float-right']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="text-muted"><?= Yii::t('backend', 'wallet.not_found')?></p>
    <?php endif; ?>
</div>
