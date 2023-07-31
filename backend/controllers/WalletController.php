<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\forms\AdjustWalletForm;
use backend\models\WalletTransactionSearch;
use common\models\User;
use common\models\Wallet;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class WalletController
 * @package backend\controllers
 */
class WalletController extends Controller
{
    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $owner = $model->getOwnerModel();
        if (!$owner instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.unsupported_wallet_type'));
        }

        $transactionSearchModel = new WalletTransactionSearch(['walletId' => $model->id]);
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('view');

        return $this->render('/user/main', [
            'model' => $owner,
            'content' => $this->renderPartial('main', [
                'user' => $owner,
                'model' => $model,
                'content' => $this->renderPartial('view', [
                    'model' => $model,
                    'transactionSearchModel' => $transactionSearchModel,
                    'transactionDataProvider' => $transactionDataProvider,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRecalculate($id)
    {
        $model = $this->findModel($id);
        $owner = $model->getOwnerModel();
        if (!$owner instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.unsupported_wallet_type'));
        }

        if (Yii::$app->request->getIsPost() && $model->recalculate()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'wallet.recalculate.success'));
        }

        return $this->redirectToRememberUrl('view', ['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAdjust($id)
    {
        $wallet = $this->findModel($id);
        $owner = $wallet->getOwnerModel();
        if (!$owner instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.unsupported_wallet_type'));
        }

        $model = new AdjustWalletForm(['wallet' => $wallet]);

        if ($model->load(Yii::$app->request->post()) && $model->process()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->redirectToRememberUrl('wallet-view', ['wallet-view', 'id' => $wallet->id]);
        }

        return $this->render('/user/main', [
            'model' => $owner,
            'content' => $this->renderPartial('main', [
                'model' => $wallet,
                'user' => $owner,
                'content' => $this->renderPartial('adjust', [
                    'model' => $model,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return Wallet|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Wallet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }
}