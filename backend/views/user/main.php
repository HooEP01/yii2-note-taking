<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\User
 * @var $content string
 * @var $this \backend\base\web\View
 */

use backend\controllers\UserController;
use common\base\enum\Gender;
use common\base\helpers\ArrayHelper;
use common\models\User;
use common\widgets\ListGroup;
use common\widgets\Tabs;
use yii\helpers\Html;

/** @var UserController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'breadcrumb.user'), 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.update');

$current = isset($current) ? $current : $this->getActionName();
$items = [
    'update' => ['label' => Yii::t('backend', 'common.tab.basic'), 'url' => ['/user/update', 'id' => $model->id]],
    'password' => ['label' => Yii::t('backend', 'user.tab.password'), 'url' => ['/user/password', 'id' => $model->id], 'visible' => !$model->getIsSystemAdmin()],
    'wallet' => ['label' => Yii::t('backend', 'user.tab.wallet'), 'url' => ['/user/wallet', 'id' => $model->id]],
    'phone' => ['label' => Yii::t('backend', 'user.tab.phone'), 'url' => ['/user/phone', 'id' => $model->id]],
    'email' => ['label' => Yii::t('backend', 'user.tab.email'), 'url' => ['/user/email', 'id' => $model->id]],
    'social' => ['label' => Yii::t('backend', 'user.tab.social'), 'url' => ['/user/social', 'id' => $model->id]],
//    'address' => ['label' => Yii::t('backend', 'user.tab.address'), 'url' => ['/user/address', 'id' => $model->id]],
    'mfa' => ['label' => Yii::t('backend', 'user.tab.mfa'), 'url' => ['/user/mfa', 'id' => $model->id]],
];

if (isset($items[$current])) {
    ArrayHelper::remove($items[$current], 'url');
    $items[$current]['active'] = true;
    $items[$current]['content'] = $content;
}

$tab = $current;
if (($pos = strpos($current, '-')) !== false) {
    $tab = substr($current, 0, $pos);
}

if (in_array($controller->id, ['wallet'])) {
    $tab = $controller->id;
}

if (in_array($tab, ['wallet', 'phone', 'email', 'social', 'address'])) {
    ArrayHelper::remove($items[$tab], 'url');
    $items[$tab]['active'] = true;
    $items[$tab]['content'] = $content;
}

?>

<div class="row">
    <div class="col-md-3">
        <div class="card card-light card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <?= Html::img($model->getAvatarImageSrc(), ['class' => 'profile-user-img img-fluid img-circle', 'alt' => 'User Avatar']); ?>
                </div>

                <h3 class="profile-username text-center text-truncate"><?= $model->getDisplayName() ?></h3>

                <p class="text-muted text-center"><?= Yii::t('backend', 'profile.member_since_{date}', ['date' => $model->getDateJoined()]) ?></p>

                <?= ListGroup::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'gender',
                            'filter' => Gender::options()
                        ],
                        [
                            'attribute' => 'dateOfBirth'
                        ],
                        [
                            'label' => Yii::t('backend', 'user.default_phone'),
                            'value' => function (User $user) {
                                return $user->phone ? $user->phone->getComplete() : null;
                            }
                        ],
                        [
                            'label' => Yii::t('backend', 'user.default_email'),
                            'value' => function (User $user) {
                                return $user->email ? $user->email->getAddress() : null;
                            }
                        ],
                    ],
                ]); ?>

                <?php if ($this->user->getIsSystemAdmin()) : ?>
                    <div style="font-size: 12px">
                        <h5>Time Zone Testing on Created:</h5>
                        <strong>Original:</strong> <?= $model->createdAt ?><br />
                        <?php $created = new \common\base\DateTime($model->createdAt) ?>
                        <strong>ISO8601:</strong> <?= $created->formatToISO8601() ?><br />
                        <strong>RFC3339:</strong> <?= $created->formatToRFC3339() ?><br />
                        <?php $created->local() ?>
                        <strong>Local Database:</strong> <?= $created->formatToDatabaseDatetime() ?><br />
                        <strong>Local ISO8601:</strong> <?= $created->formatToISO8601() ?><br />
                        <strong>Local RFC3339:</strong> <?= $created->formatToRFC3339() ?><br />
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <?= Tabs::widget(['items' => $items]); ?>
    </div>
</div>
