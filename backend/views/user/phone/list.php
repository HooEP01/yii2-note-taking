<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $model \common\models\User
 * @var $searchModel \backend\models\UserPhoneSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use backend\controllers\UserController;
use common\base\grid\GridView;
use common\models\UserPhone;
use yii\helpers\Html;

/** @var UserController $controller */
$controller = $this->context;

?>

<div class="card mb-0 h-100">
    <div class="card-header">
        <div class="card-tools">
            <?= Html::a('<i class="fas fa-plus"></i>', ['phone-create', 'id' => $model->id], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['phone', 'id' => $model->id], ['encode' => false, 'class' => 'btn btn-tool']); ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'yii-grid card-body p-0'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn', 'headerOptions' => ['style' => 'width: 25px;']
            ],
            [
                'attribute' => 'complete',
                'format' => 'raw',
                'value' => function (UserPhone $phone) {
                    $value = $phone->getComplete();
                    if ($phone->getIsDefault()) {
                        $value .= '<span class="float-right badge badge-danger">Default</span>';
                    }

                    return $value;
                }
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isVerified'
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isActive',
                'visible' => $this->user->getIsSuperAdmin(),
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'fas fa-edit']);
                        return Html::a($icon, ['phone-update', 'id' => $model->id], ['title' => 'Update', 'class' => 'grid-link']);
                    },
                ],
                'dropdownItems' => [
                    'default' => function ($url, UserPhone $model, $key) {
                        if (!$model->getIsDefault() && $model->getIsActive()) {
                            return [
                                'label' => '<i class="fas fa-star mr-3"></i>Set as Default',
                                'url' => ['phone-default', 'id' => $model->id],
                                'encode' => false,
                                'options' => ['class' => 'grid-link'],
                                'linkOptions' => ['data-method' => 'post'],
                            ];
                        }

                        return [
                            'label' => '<i class="fas fa-star mr-3"></i>Set as Default',
                            'url' => 'javascript:void(0)',
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                            'linkOptions' => ['class' => 'disabled']
                        ];
                    },
                    'delete' => function ($url, UserPhone $model, $key) {
                        return [
                            'label' => $model->getIsActive() ? '<i class="far fa-trash-alt mr-3"></i>Delete' : '<i class="fas fa-undo mr-3"></i>Restore',
                            'url' => [$model->getIsActive() ? 'phone-delete' : 'phone-restore', 'id' => $model->id],
                            'encode' => false,
                            'options' => ['class' => 'grid-link'],
                            'linkOptions' => ['data-method' => 'post', 'data-confirm' => $model->getIsActive() ? Yii::t('backend', 'model.delete.confirmation') : Yii::t('backend', 'model.restore.confirmation')]
                        ];
                    },
                ],
            ],
        ],
    ]); ?>
</div>



