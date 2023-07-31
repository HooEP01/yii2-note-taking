<?php

namespace backend\controllers;

use common\models\Folder;
use backend\models\FolderSearch;
use common\base\enum\NotePriorityType;
use common\models\Note;
use common\models\NoteSearch;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * FolderController implements the CRUD actions for Folder model.
 */
class FolderController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Folder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FolderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Folder model.
     * @param int $id #
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('main', [
            'model' => $model,
            'content' => $this->renderPartial('view', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * Creates a new Folder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Folder();

        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Folder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id #
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Folder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id #
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionAjaxSearch($q = null, $id = null)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results' => [['id' => '', 'text' => '']]
        ];

        if (!is_null($q)) {
            $query = Folder::find()->alias('t')
                ->andWhere([
                    'or',
                    ['ilike', 'title', $q],
                ]);

            $data = [];
            foreach ($query->all() as $folder) {
                $data[] = ['id' => $folder->id, 'text' => $folder->title];
            }

            $result['results'] = $data;
        } elseif ($id > 0) {
            $folder = Folder::findOne($id);
            if ($folder !== null) {
                $result['results'] = [['id' => $folder->id, 'text' => $folder->title]];
            }
        }

        return $result;
    }


    public function actionNote($id)
    {
        $model = $this->findModel($id);

        $searchModel = new NoteSearch(['folder' => $model]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('main', [
            'model' => $model,
            'content' => $this->renderPartial('note/main', [
                'folder' => $model,
                'content' => $this->renderPartial('note/list', [
                    'folder' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * Displays a single Note model.
     * @param int $id #
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionNoteView($id)
    {
        $model = $this->findNoteModel($id);
        $folder = $this->findModel($model->folder_id);

        return $this->render('main', [
            'model' => $folder,
            'content' => $this->renderPartial('note/main', [
                'folder' => $folder,
                'model' => $model,
                'content' => $this->renderPartial('note/view', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    public function actionNoteCreate($id)
    {
        $folder = $this->findModel($id);

        $model = new Note();
        $model->folder_id = $id;
        $model->priority = NotePriorityType::LOW;
        // $model->due_date = DateTime::now();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['folder/note', 'id' => $folder->id]);
        }

        return $this->render('main', [
            'model' => $folder,
            'content' => $this->renderPartial('note/main', [
                'folder' => $folder,
                'content' => $this->renderPartial('note/_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    public function actionNoteUpdate($id)
    {
        $model = $this->findNoteModel($id);
        $folder = $this->findModel($model->folder_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['folder/note', 'id' => $model->folder_id]);
        }

        return $this->render('main', [
            'model' => $folder,
            'content' => $this->renderPartial('note/main', [
                'folder' => $folder,
                'model' => $model,
                'content' => $this->renderPartial('note/_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * Finds the Folder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id #
     * @return Folder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Folder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findNoteModel($id)
    {
        if (($model = Note::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
