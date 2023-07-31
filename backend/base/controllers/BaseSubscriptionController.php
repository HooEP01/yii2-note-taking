<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\models\SubscriptionSearch;
use common\base\enum\SubscriptionStatus;
use common\models\Subscription;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Class BaseSubscriptionController
 * @property string $type
 * @package backend\controllers
 */
abstract class BaseSubscriptionController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new SubscriptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//subscription/main', [
                'content' => $this->renderPartial('//subscription/list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Subscription(['type' => $this->type, 'precision' => 2, 'status' => SubscriptionStatus::DRAFT]);
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Created !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//subscription/main', [
                'content' => $this->renderPartial('//subscription/create', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param string $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//subscription/main', [
                'model' => $model,
                'content' => $this->renderPartial('//subscription/_form', [
                    'model' => $model,
                ])
            ])
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

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//subscription/main', [
                'model' => $model,
                'content' => $this->renderPartial('//subscription/image', [
                    'model' => $model,
                    'image' => $image,
                ]),
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
        if (Yii::$app->request->getIsPost() && $this->findModel($id)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToDefaultListUrl();
    }

    /**
     * @param string $id
     * @return Subscription|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Subscription::findOne($id);
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.subscription');
    }

    /**
     * @return string
     */
    abstract protected function getType();
}