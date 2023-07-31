<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;

use backend\base\web\Controller;
use backend\models\PageContentSearch;
use common\base\enum\EditMode;
use common\base\helpers\Url;
use common\models\PageContent;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class PageController
 * @package backend\controllers
 */
class PageController extends Controller
{
    /**
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new PageContentSearch();
        $searchModel->isActive = true;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->rememberUrl('list');

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new PageContent();
        $model->position = 999;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id, 'mode' => EditMode::PREVIEW]);
        }

        return $this->render('_form', [
            'model' => $model,
            'mode' => EditMode::EDIT
        ]);
    }

    /**
     * @param integer $id
     * @param string $mode
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $mode = EditMode::EDIT)
    {
        $model = $this->findModel($id);

        if (!in_array($mode, EditMode::values())) {
            $mode = EditMode::EDIT;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->redirect(Url::current(['mode' => EditMode::PREVIEW]));
        }

        return $this->render('_form', [
            'model' => $model,
            'mode' => $mode
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionToggle($id)
    {
        if (Yii::$app->request->getIsPost() && $this->findModel($id)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToDefaultListUrl();
    }

    /**
     * @param integer $id
     * @return PageContent
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = PageContent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.page');
    }
}