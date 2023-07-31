<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\models\ImageSearch;
use common\models\Image;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class BaseImageController
 * @property string $code
 * @property string $title
 * @package backend\base\controllers
 */
abstract class BaseImageController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new ImageSearch(['code' => $this->code]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->rememberUrl('list');

        return $this->render('//image/list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Image(['code' => $this->code]);
        $model->setScenario('upload');

        if ($model->load(Yii::$app->request->post()) && $model->upload()) {
            $model->updateCallToAction(Yii::$app->request->post());

            Yii::$app->session->setFlash('success', 'Image Created !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//image/create', [
            'model' => $model
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('optional');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->upload();
            $model->updateCallToAction(Yii::$app->request->post());

            Yii::$app->session->setFlash('success', 'Image Updated !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//image/update', [
            'model' => $model
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($id)
    {
        $model = $this->findModel($id);
        if ($model->toggleActive()) {
            Yii::$app->session->setFlash('success', 'Image Toggled !!');
        }

        return $this->redirectToRememberUrl('list', [['list']]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.image');
    }

    /**
     * @param int $id
     * @return Image|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    abstract protected function getCode();
}