<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\models\StateSearch;
use backend\base\web\Controller;
use common\models\Country;
use common\models\State;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class StateController
 * @package backend\controllers
 */
class StateController extends Controller
{
    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionList()
    {
        $searchModel = new StateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/state/main', [
                'content' => $this->renderPartial('/state/list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $model = new State();
        $model->position = 999;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'code' => $model->code]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/state/main', [
                'content' => $this->renderPartial('/state/_form', [
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
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'code' => $model->code]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/state/main', [
                'model' => $model,
                'content' => $this->renderPartial('/state/_form', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param string $code
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($code)
    {
        $model = $this->findModel($code);
        if (Yii::$app->request->getIsPost() && $model->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('list', ['list']);
    }


    /**
     * @param string $code
     * @return State|null
     * @throws NotFoundHttpException
     */
    protected function findModel($code)
    {
        if (($model = State::findOne($code)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @param string $code
     * @return Country|null
     * @throws NotFoundHttpException
     */
    protected function findCountry($code)
    {
        if (($model = Country::findOne($code)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Yii::t('backend', 'breadcrumb.state');
    }
}