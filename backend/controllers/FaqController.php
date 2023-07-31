<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\models\FaqSearch;
use common\base\enum\EditMode;
use common\base\enum\FaqType;
use common\base\helpers\Url;
use common\models\Faq;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Class FaqController
 * @package backend\controllers
 */
class FaqController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new FaqSearch(['isActive' => true]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Faq();
        $model->type = FaqType::GENERAL;
        $model->position = 999;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id, 'mode' => EditMode::PREVIEW]);
        }

        return $this->render('_form', [
            'model' => $model,
            'mode' => EditMode::EDIT,
        ]);
    }

    /**
     * @param $id
     * @param string $mode
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id, $mode = EditMode::EDIT)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->redirect(Url::current(['mode' => EditMode::PREVIEW]));
        }

        return $this->render('_form', [
            'model' => $model,
            'mode' => $mode,
        ]);

    }

    /**
     * @param string $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionToggle($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->getIsPost() && $model->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('list', ['list', 'active' => !$model->getIsActive()]);
    }

    /**
     * @param $id
     * @return ActiveRecord|Faq|null
     * @throws BadRequestHttpException
     */
    protected function findModel($id)
    {
        if (($model = Faq::findOne($id)) !== null) {
            return $model;
        }

        throw new BadRequestHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.faq');
    }
}