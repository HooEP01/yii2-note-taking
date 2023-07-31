<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;

use backend\base\web\Controller;
use backend\forms\LoginForm;
use backend\forms\MfaLoginForm;
use common\models\User;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @return array
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['login', 'mfa', 'error',],
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => '\yii\filters\VerbFilter',
            'actions' => [
                'logout' => ['post'],
            ],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string|\yii\console\Response|Response
     */
    public function actionLogin()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if (($result = $model->login()) instanceof User) {
                return $this->redirect(['mfa', 'data' => $model->generateMfaAuthData()]);
            } elseif ($result === true) {
                return $this->goBack();
            }
        }


        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    /**
     * @param null|string $data
     * @return SiteController|string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMfa($data)
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new MfaLoginForm(['data' => $data]);
        if (!$model->getIsDataValid()) {
            throw new BadRequestHttpException('Invalid Request !');
        }

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        return $this->render('mfa', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return \yii\web\Response
     */
    public function actionFlushCache()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'cache.flushed'));

        return $this->redirectToReferrer(['site/index']);
    }
}
