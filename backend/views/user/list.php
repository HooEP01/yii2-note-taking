<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $searchModel \backend\models\UserSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\enum\Gender;
use common\base\enum\UserRole;
use common\base\grid\GridView;
use common\models\User;
use yii\helpers\Html;

/** @var \backend\controllers\UserController $controller */
$controller = $this->context;

$this->title = Yii::t('backend', 'breadcrumb.user');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.user');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.list');

?>

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <?= Html::a('<i class="nav-icon fas fa-plus"></i>', ['create'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['list'], ['encode' => false, 'class' => 'btn btn-tool']); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'yii-grid card-body p-0'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width: 25px; text-align: center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function (User $user) {
                    return $user->getDisplayName();
                }
            ],
            [
                'attribute' => 'roles',
                'filter' => UserRole::options(),
                'format' => 'raw',
                'value' => function (User $user) {
                    if (empty($user->roleItems)) {
                        return Yii::$app->formatter->nullDisplay;
                    }

                    return implode(', ', $user->roleItems);
                }
            ],
            [
                'attribute' => 'gender',
                'filter' => Gender::options(),
            ],
            [
                'attribute' => 'phoneId',
                'format' => 'raw',
                'value' => function (User $user) {
                    return $user->phone ? $user->phone->complete : null;
                }
            ],
            [
                'attribute' => 'emailId',
                'value' => function (User $user) {
                    return $user->email ? $user->email->address : null;
                }
            ],
            [
                'class' => 'common\base\grid\DatetimeColumn',
                'attribute' => 'createdAt',
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isActive',
                'visible' => $this->user->getIsSuperAdmin(),
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update} {toggle}',
                'dropdownItems' => [
                    'password' => function ($url, User $model, $key) {
                        return [
                            'label' => '<i class="fas fa-user-lock mr-3"></i>Password',
                            'url' => ['password', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                            'visible' => !$model->getIsSuperAdmin(),
                        ];
                    },
                    'wallet' => function ($url, User $model, $key) {
                        return [
                            'label' => '<i class="fas fa-wallet mr-3"></i>Wallet',
                            'url' => ['wallet', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                        ];
                    },
                    'phone' => function ($url, User $model, $key) {
                        return [
                            'label' => '<i class="fas fa-phone mr-3"></i>Phone',
                            'url' => ['phone', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                        ];
                    },
                    'email' => function ($url, User $model, $key) {
                        return [
                            'label' => '<i class="fas fa-envelope mr-3"></i>Email',
                            'url' => ['email', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                        ];
                    },
                    'social' => function ($url, User $model, $key) {
                        return [
                            'label' => '<i class="fab fa-facebook-square mr-3"></i>Social Account',
                            'url' => ['social', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                        ];
                    },
                ],
            ],
        ],
    ]); ?>
</div>
