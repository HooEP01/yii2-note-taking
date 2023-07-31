<?php
/**
 * @author RYU Chua <me@ryu.my>
 */

namespace backend\base\controllers;

use backend\base\web\Controller;
use backend\forms\NewAccountForm;
use backend\models\AccountSearch;
use common\base\enum\AccountType;
use common\models\Account;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Class BaseAccountController
 * @property string $type
 * @package backend\controllers
 */
abstract class BaseAccountController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new AccountSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('//account/list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new NewAccountForm(['type' => $this->type]);
        if ($model->load(Yii::$app->request->post()) && $model->process()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->account->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @param string $id
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('//account/main', [
            'model' => $model,
            'content' => $this->renderPartial('update', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param string $id
     */
    public function actionContact($id)
    {
        $model = $this->findModel($id);

        return $this->render('//account/main', [
            'model' => $model,
            'content' => $this->renderPartial('//account/contact', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param string $id
     */
    public function actionSubscription($id)
    {
        $model = $this->findModel($id);
        return $this->render('//account/main', [
            'model' => $model,
            'content' => $this->renderPartial('//account/subscription', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionToggle($id)
    {
        if (Yii::$app->request->getIsPost() && $this->findModel($id)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('list', ['list']);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUser($id)
    {
        $model = $this->findModel($id);
        $this->rememberUrl('user');


        return $this->render('//account/main', [
            'model' => $model,
            'content' => $this->renderPartial('//account/user', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param string $id
     * @return Account|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (AccountType::isValid($this->type)) {
            return AccountType::resolve($this->type) . ' ' . Yii::t('backend', 'breadcrumb.account');
        }

        return Yii::t('backend', 'breadcrumb.account');
    }

    /**
     * @return string
     */
    abstract protected function getType();
}