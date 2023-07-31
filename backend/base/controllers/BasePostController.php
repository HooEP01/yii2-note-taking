<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\models\PostSearch;
use common\base\enum\PostStatus;
use common\models\Post;
use yii\base\Response;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class BasePostController
 * @property string $type
 * @property string $name
 * @package backend\base\controllers
 */
abstract class BasePostController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new PostSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->rememberUrl('list');

        return $this->render('//post/list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Post(['type' => $this->type, 'status' => PostStatus::DRAFT]);
        $model->setScenario(sprintf('%s-%s', $this->type, $this->getActionName()));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Created !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//post/main', [
            'model' => $model,
            'content' => $this->renderPartial('//post/create', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(sprintf('%s-%s', $this->type, $this->getActionName()));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//post/main', [
            'model' => $model,
            'content' => $this->renderPartial('//post/update', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionContent($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }


        return $this->render('//post/main', [
            'model' => $model,
            'content' => $this->renderPartial('//post/content', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param string $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionImage($id)
    {
        $model = $this->findModel($id);
        $image = $model->getImageModel();
        $image->setScenario('upload');

        if ($image->load(Yii::$app->request->post()) && $image->upload()) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
                return $this->refresh();
            }
        }

        return $this->render('//post/main', [
            'model' => $model,
            'content' => $this->renderPartial('//post/image', [
                'model' => $model,
                'image' => $image,
            ]),
        ]);
    }

    /**
     * @param string $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($id)
    {
        $model = $this->findModel($id);
        if ($model->toggleActive()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Toggled !!');
        }

        return $this->redirectToRememberUrl('list', [['list']]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.post');
    }

    /**
     * @param string $id
     * @return Post|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Post::find()->id($id)->type($this->type)->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    abstract protected function getType();
}