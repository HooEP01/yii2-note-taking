<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\models\CurrencyRateSearch;
use common\models\CurrencyRate;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class CurrencyRateController
 * @package backend\controllers
 */
class CurrencyRateController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new CurrencyRateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/currency-rate/main', [
                'content' => $this->renderPartial('/currency-rate/list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CurrencyRate();
        $model->precision = 6;
        $model->conversionRateValue = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/currency-rate/main', [
                'model' => $model,
                'content' => $this->renderPartial('_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/currency-rate/main', [
                'model' => $model,
                'content' => $this->renderPartial('_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($id)
    {
        if (Yii::$app->request->post() && $this->findModel($id)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToDefaultListUrl();
    }

    /**
     * @param $id
     * @return CurrencyRate|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = CurrencyRate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.currency_rate');
    }
}