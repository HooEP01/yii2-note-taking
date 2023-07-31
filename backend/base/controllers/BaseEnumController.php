<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\models\SystemEnumSearch;
use common\models\SystemEnum;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class BaseEnumController
 * @property string $type
 * @property string $name
 * @package backend\base\controllers
 */
abstract class BaseEnumController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new SystemEnumSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->rememberUrl('list');

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//enum/main', [
                'content' => $this->renderPartial('//enum/list', [
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
        $model = new SystemEnum(['type' => $this->type]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Created !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//enum/main', [
                'content' => $this->renderPartial('//enum/create', [
                    'model' => $model,
                ])
            ]),
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//enum/main', [
                'model' => $model,
                'content' => $this->renderPartial('//enum/_form', [
                    'model' => $model,
                ])
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

        return $this->render('//setup/main', [
            'content' => $this->renderPartial('//enum/main', [
                'model' => $model,
                'content' => $this->renderPartial('//enum/image', [
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
        return Yii::t('backend', 'breadcrumb.enum');
    }

    /**
     * @param string $id
     * @return SystemEnum|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = SystemEnum::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    abstract protected function getType();
}