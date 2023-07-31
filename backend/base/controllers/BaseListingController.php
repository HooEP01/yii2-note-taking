<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\models\ListingSearch;
use common\base\enum\EditMode;
use common\base\enum\ImageCacheStatus;
use common\base\enum\ListingStatus;
use common\base\helpers\Url;
use common\models\Image;
use common\models\ImageCache;
use common\models\Listing;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\web\HttpException;

/**
 * Class BaseListingController
 * @property string $kind
 * @property string $name
 * @package backend\base\controllers
 */
abstract class BaseListingController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new ListingSearch(['kind' => $this->kind]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->rememberUrl('list');

        return $this->render('//listing/list', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Listing(['kind' => $this->kind, 'precision' => 2, 'status' => ListingStatus::DRAFT]);
        $model->setScenario(sprintf('%s-%s', $this->kind, $this->getActionName()));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Created !!');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('create', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(sprintf('%s-%s', $this->kind, $this->getActionName()));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('update', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionDescription($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/description', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @param string $mode
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionContent($id, $mode = EditMode::EDIT)
    {
        return $this->redirect(['description', 'id' => $id]);

        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if (!in_array($mode, EditMode::values())) {
            $mode = EditMode::EDIT;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->redirect(Url::current(['mode' => EditMode::PREVIEW]));
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/content', [
                'model' => $model,
                'mode' => $mode,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionLocation($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/location', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPricing($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refreshCacheData();
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('pricing', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionAmenity($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/amenity', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param string $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionFeature($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/feature', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param string $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($this->getActionName());

        if (Yii::$app->request->getIsPost()) {
            $token = Yii::$app->request->getBodyParam('token');

            if ($token === $model->token) {
                if ($model->processPublish()) {
                    Yii::$app->session->setFlash('success', 'Published Successfully !');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to publish listing !!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Invalid Publish Token !');
            }

            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/publish', [
                'model' => $model,
            ]),
        ]);
    }

    /**
     * @param string $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionImage($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $this->getName() . ' Updated !!');
            return $this->refresh();
        }

        return $this->render('//listing/main', [
            'model' => $model,
            'content' => $this->renderPartial('//listing/image', [
                'model' => $model,
            ]),
        ]);
    }

    public function actionGenerateWatermark($id)
    {

        if (!$this->user->getIsSystemAdmin()) {
            throw new HttpException(403, "You are not allowed to perform this action.");
        }

        $model = $this->findModel($id);
        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $images = Image::find()->owner($model)->active()->all();
            foreach ($images as $image) {
                $image->generateWatermark();
                $watermarkImage = $image->getWatermarkModel();
                $valid = $valid && $watermarkImage->purgeCaches();
                $watermarkImage->cacheIndex = null;
                $valid = $valid && $watermarkImage->save(false, ['cacheIndex']);
            }

            $valid ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            Yii::error($e);
        }


        Yii::$app->session->setFlash('success', 'Image On Process !!');
        return $this->redirect(['image', 'id' => $model->id]);
    }

    /**
     * @param string $id
     * @return Response
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
     * @return Listing|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Listing::find()->id($id)->kind($this->kind)->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    abstract protected function getKind();
}
