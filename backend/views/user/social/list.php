<?php
/**
 * @copyright Copyright (c) Hustle Hero
 *
 * @var $user \common\models\User
 * @var $searchModel \backend\models\UserSocialSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \backend\base\web\View
 */

use common\base\enum\SocialChannel;
use common\base\grid\GridView;
use common\models\UserSocial;
use yii\helpers\Html;

?>

<div class="card mb-0 h-100">
    <div class="card-header">
        <div class="card-tools">
            <?= Html::a('<i class="fas fa-plus"></i>', ['social-create', 'id' => $user->id], ['encode' => false, 'class' => 'btn btn-tool']); ?>
            <?= Html::a('<i class="nav-icon fas fa-sync"></i>', ['social', 'id' => $user->id], ['encode' => false, 'class' => 'btn btn-tool']); ?>
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
                'attribute' => 'channel',
                'filter' => SocialChannel::options()
            ],
            [
                'attribute' => 'channelId',
            ],
            [
                'attribute' => 'channelName',
            ],
            [
                'class' => 'common\base\grid\BooleanColumn',
                'attribute' => 'isVerified'
            ],
            [
                'class' => 'common\base\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'fas fa-edit']);
                        return Html::a($icon, ['social-update', 'id' => $model->id], ['title' => 'Update', 'class' => 'grid-link']);
                    },
                ],
                'dropdownItems' => [
                    'delete' => function ($url,UserSocial $model, $key) {
                        return [
                            'label' => $model->getIsActive() ? '<i class="far fa-trash-alt mr-3"></i>Delete' : '<i class="fas fa-undo mr-3"></i>Restore',
                            'url' => ['social-toggle', 'id' => $model->id],
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



