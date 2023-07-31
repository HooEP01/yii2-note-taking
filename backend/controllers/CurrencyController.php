<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\models\CurrencySearch;
use common\models\Currency;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class CurrencyController
 * @package backend\controllers
 */
class CurrencyController extends Controller
{
    /**
    * @return string
    */
    public function actionList()
    {
        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/currency/main', [
                'content' => $this->renderPartial('/currency/list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ]),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Currency();
        $model->precision = 2;
        $model->decimalPoint = '.';
        $model->position = 999;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'code' => $model->code]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/currency/main', [
                'content' => $this->renderPartial('/currency/_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param string $code
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($code)
    {
        $model = $this->findModel($code);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('/setup/main', [
            'model' => $model,
            'content' => $this->renderPartial('/currency/main', [
                'content' => $this->renderPartial('/currency/_form', [
                    'model' => $model
                ])
            ]),
        ]);
    }

    /**
     * @param string $code
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($code)
    {
        if (Yii::$app->request->getIsPost() && $this->findModel($code)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToDefaultListUrl();
    }

    /**
     * @param string $code
     * @return Currency|null
     * @throws NotFoundHttpException
     */
    protected function findModel($code)
    {
        $model = Currency::findOne($code);
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
        return Yii::t('backend', 'breadcrumb.currency');
    }
}