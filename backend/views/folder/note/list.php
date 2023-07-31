<?php

use common\models\Note;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\NoteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-index p-2">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Note', ['note-create', 'id' => $folder->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'folder_id',
            'title',
            'description:ntext',
            // 'tags:ntext',
            'priority',
            //'due_date',
            //'status',
            //'createdBy',
            //'createdAt',
            //'updatedBy',
            //'updatedAt',
            //'deletedBy',
            //'deletedAt',
            //'isActive:boolean',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, Note $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-eye']);
                        return Html::a($icon, ['/folder/note-view', 'id' => $model->id], ['title' => 'View', 'class' => 'grid-link']);
                    },
                    'update' => function ($url, Note $model, $key) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-edit']);
                        return Html::a($icon, ['/folder/note-update', 'id' => $model->id], ['title' => 'Edit', 'class' => 'grid-link']);
                    },
                ],
            ], 
        ],
    ]); ?>


</div>
