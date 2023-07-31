<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\models\LanguageSearch;
use common\models\Language;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class LanguageController
 * @package backend\controllers
 */
class LanguageController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/language/main', [
                'content' => $this->renderPartial('/language/list', [
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
        $model = new Language();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'code' => $model->code]);
        }

        return $this->render('/setup/main', [
            'content' => $this->renderPartial('/language/main', [
                'model' => $model,
                'content' => $this->renderPartial('/language/create', [
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
            'content' => $this->renderPartial('/language/main', [
                'model' => $model,
                'content' => $this->renderPartial('/language/update', [
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
    public function actionImage($code)
    {
        $model = $this->findModel($code);

        $image = $model->getImageModel();
        $image->setScenario('upload');

        if ($image->load(Yii::$app->request->post()) && $image->upload()) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
                return $this->refresh();
            }
        }

        return $this->render('/setup/main', [
            'model' => $model,
            'content' => $this->renderPartial('/language/main', [
                'model' => $model,
                'content' => $this->renderPartial('/language/image', [
                    'model' => $model,
                    'image' => $image,
                ]),
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
     * @return Language|null
     * @throws NotFoundHttpException
     */
    protected function findModel($code)
    {
        $model = Language::findOne($code);
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
        return Yii::t('backend', 'breadcrumb.language');
    }
}