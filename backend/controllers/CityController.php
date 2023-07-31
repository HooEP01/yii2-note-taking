<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\models\CitySearch;
use common\models\City;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class CityController
 * @package backend\controllers
 */
class CityController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/city/main', [
                'content' => $this->renderPartial('/city/list', [
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
        $model = new City();
        $model->position = 999;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/city/main', [
                'model' => $model,
                'content' => $this->renderPartial('/city/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param string $code
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/city/main', [
                'model' => $model,
                'content' => $this->renderPartial('/city/_form', [
                    'model' => $model,
                ])
            ])
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
        if (Yii::$app->request->getIsPost() && $model->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('list', ['list']);
    }


    /**
     * @param string $code
     * @return City|null
     * @throws NotFoundHttpException
     */
    protected function findModel($code)
    {
        if (($model = City::findOne($code)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.city');
    }
}