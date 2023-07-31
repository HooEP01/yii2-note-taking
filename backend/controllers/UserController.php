<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;

use backend\base\web\Controller;
use backend\forms\ResetPasswordForm;
use backend\models\AddressSearch;
use backend\models\UserEmailSearch;
use backend\models\UserPhoneSearch;
use backend\models\UserSearch;
use backend\models\UserSocialSearch;
use common\base\enum\CountryCode;
use common\base\enum\CurrencyCode;
use common\base\enum\Gender;
use common\base\enum\LanguageCode;
use common\base\enum\MobilePrefix;
use common\base\enum\NameFormat;
use common\base\enum\ScenarioCode;
use common\base\enum\UserStatus;
use common\base\helpers\StringHelper;
use common\models\Address;
use common\models\User;
use common\models\UserEmail;
use common\models\UserPhone;
use common\models\UserSocial;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class UserController
 * @package backend\controllers
 */
class UserController extends Controller
{
    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new UserSearch(['isActive' => true]);
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $this->rememberUrl('list');

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new User();
        $model->status = UserStatus::ACTIVE;
        $model->languageCode = LanguageCode::ENGLISH;
        $model->nameFormat = NameFormat::FIRST_LAST;
        $model->gender = Gender::OTHER;
        $model->currencyCode = CurrencyCode::AUSTRALIAN_DOLLAR;
        $model->countryCode = CountryCode::MALAYSIA;
        $model->setPassword(StringHelper::randomString());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
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

        return $this->render('main', [
            'model' => $model,
            'content' => $this->renderPartial('update', [
                'model' => $model,
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
        if (Yii::$app->request->getIsPost() && $this->findModel($id)->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('list', ['list']);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionPassword($id)
    {
        $user = $this->findModel($id);
        if ($user->getIsSuperAdmin()) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'user.password.not_allowed'));
            return $this->redirect(['update', 'id' => $user->id]);
        }

        $model = new ResetPasswordForm(['user' => $user]);

        if ($model->load(Yii::$app->request->post()) && $model->process()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('password', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionWallet($id)
    {
        $user = $this->findModel($id);

        $user->getDefaultWallet();
        $user->getPointWallet();

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('/wallet/main', [
                'user' => $user,
                'content' => $this->renderPartial('/wallet/list', [
                    'user' => $user
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPhone($id)
    {
        $user = $this->findModel($id);

        $searchModel = new UserPhoneSearch(['userId' => $user->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('phone');

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('phone/main', [
                'user' => $user,
                'content' => $this->renderPartial('phone/list', [
                    'model' => $user,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneCreate($id)
    {
        $user = $this->findModel($id);
        $model = new UserPhone();
        $model->userId = $user->id;
        $model->prefix = MobilePrefix::MALAYSIA;
        $model->isVerified = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['phone-update', 'id' => $model->id]);
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('phone/main', [
                'user' => $user,
                'content' => $this->renderPartial('phone/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneUpdate($id)
    {
        $model = $this->findPhone($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('main', [
            'model' => $model->user,
            'content' => $this->renderPartial('phone/main', [
                'user' => $model->user,
                'model' => $model,
                'content' => $this->renderPartial('phone/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneDefault($id)
    {
        $model = $this->findPhone($id);
        if (Yii::$app->request->getIsPost() && $model->getIsActive() && !$model->getIsDefault() && $model->setAsDefault()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.default.success'));
        }

        return $this->redirectToRememberUrl('user-phone', ['phone', 'id' => $model->userId]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneToggle($id)
    {
        $model = $this->findPhone($id);
        if (Yii::$app->request->getIsPost() && $model->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('user-phone', ['phone', 'id' => $model->userId]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneDelete($id)
    {
        $model = $this->findPhone($id);
        if (Yii::$app->request->getIsPost()) {
            if ($model->getIsActive()) {
                $model->softDelete();
            }
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.delete.success'));
        }

        return $this->redirectToRememberUrl('user-phone', ['phone', 'id' => $model->userId]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneRestore($id)
    {
        $model = $this->findPhone($id);
        if (Yii::$app->request->getIsPost()) {
            if (!$model->getIsActive()) {
                $model->softRestore();
            }

            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.delete.success'));
        }

        return $this->redirectToRememberUrl('user-phone', ['phone', 'id' => $model->userId]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEmail($id)
    {
        $user = $this->findModel($id);

        $searchModel = new UserEmailSearch(['userId' => $user->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('user-email');

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('email/main', [
                'user' => $user,
                'content' => $this->renderPartial('email/list', [
                    'user' => $user,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ]),
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEmailCreate($id)
    {
        $user = $this->findModel($id);

        $model = new UserEmail();
        $model->userId = $user->id;
        $model->isVerified = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['email-update', 'id' => $model->id]);
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('email/main', [
                'user' => $user,
                'content' => $this->renderPartial('email/_form', [
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
    public function actionEmailUpdate($id)
    {
        $model = $this->findEmail($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('main', [
            'model' => $model->user,
            'content' => $this->renderPartial('email/main', [
                'user' => $model->user,
                'model' => $model,
                'content' => $this->renderPartial('email/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEmailDefault($id)
    {
        $model = $this->findEmail($id);

        if (Yii::$app->request->getIsPost() && $model->getIsActive() && !$model->getIsDefault() && $model->setAsDefault()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.default.success'));
        }

        return $this->redirectToRememberUrl('user-email', ['email', 'id' =>$model->userId]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEmailToggle($id)
    {
        $model = $this->findEmail($id);

        if (Yii::$app->request->getIsPost()) {
            if ($model->getIsActive()) {
                if ($model->softDelete()) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
                }
            } else {
                if ($model->softRestore()) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
                }
            }
        }

        return $this->redirectToRememberUrl('user-email', ['email', 'id' =>$model->userId]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSocial($id)
    {
        $user = $this->findModel($id);

        $searchModel = new UserSocialSearch(['userId' => $user->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('user-social');

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('social/main', [
                'user' => $user,
                'content' => $this->renderPartial('social/list', [
                    'user' => $user,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSocialCreate($id)
    {
        $user = $this->findModel($id);

        $model = new UserSocial();
        $model->userId = $user->id;
        $model->isVerified = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['social-update', 'id' => $model->id]);
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('social/main', [
                'model' => $model,
                'user' => $user,
                'content' => $this->renderPartial('social/_form', [
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
    public function actionSocialUpdate($id)
    {
        $model = $this->findSocial($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('main', [
            'model' => $model->user,
            'content' => $this->renderPartial('social/main', [
                'model' => $model,
                'user' => $model->user,
                'content' => $this->renderPartial('social/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSocialToggle($id)
    {
        $model = $this->findSocial($id);

        if (Yii::$app->request->getIsPost()) {
            if ($model->getIsActive()) {
                if ($model->softDelete()) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
                }
            } else {
                if ($model->softRestore()) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
                }
            }
        }

        return $this->redirectToRememberUrl('user-social', ['social', 'id' =>$model->userId]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAddress($id)
    {
        $user = $this->findModel($id);

        $searchModel = new AddressSearch(['owner' => $user, 'isActive' => true]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->rememberUrl('user-address');

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('address/main', [
                'user' => $user,
                'content' => $this->renderPartial('address/list', [
                    'user' => $user,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ])
            ])
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAddressCreate($id)
    {
        $user = $this->findModel($id);

        $address = Address::factory($user);

        if ($address->load(Yii::$app->request->post()) && $address->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.create.success'));
            return $this->redirect(['address-update', 'id' => $address->id]);
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('address/main', [
                'user' => $user,
                'content' => $this->renderPartial('address/_form', [
                    'model' => $address,
                ])
            ]),
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAddressUpdate($id)
    {
        $model = $this->findAddress($id);

        $user = $model->getOwnerModel();
        if (!$user || !$user instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.update.success'));
            return $this->refresh();
        }

        return $this->render('main', [
            'model' => $user,
            'content' => $this->renderPartial('address/main', [
                'user' => $user,
                'model' => $model,
                'content' => $this->renderPartial('address/_form', [
                    'model' => $model,
                ])
            ]),
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAddressDefault($id)
    {
        $model = $this->findAddress($id);

        $user = $model->getOwnerModel();
        if (!$user || !$user instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
        }

        if (Yii::$app->request->getIsPost() && $model->getIsActive() && !$model->getIsDefault() && $model->setAsDefault()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.default.success'));
        }

        return $this->redirectToRememberUrl('user-address', ['address', 'id' => $user->id]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAddressToggle($id)
    {
        $model = $this->findAddress($id);

        $user = $model->getOwnerModel();
        if (!$user || !$user instanceof User) {
            throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
        }

        if (Yii::$app->request->getIsPost() && $model->toggleActive()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'model.toggle.success'));
        }

        return $this->redirectToRememberUrl('user-address', ['address', 'id' => $user->id]);
    }

    public function actionMfa($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->getIsDelete()) {
            $model->authMfaToken = null;
            $model->save(false, ['authMfaToken']);
            $this->refresh();
        } elseif (Yii::$app->request->getIsPost()) {
            $model->resetAuthMfaToken();
            $this->refresh();
        }

        return $this->render('main', [
            'model' => $model,
            'content' => $this->renderPartial('mfa', [
                'model' => $model,
            ])
        ]);
    }

    /**
     * @param $id
     * @return User|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @param $id
     * @return UserPhone|null
     * @throws NotFoundHttpException
     */
    protected function findPhone($id)
    {
        if (($model = UserPhone::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @param $id
     * @return UserEmail|null
     * @throws NotFoundHttpException
     */
    protected function findEmail($id)
    {
        if (($model = UserEmail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @param $id
     * @return UserSocial|null
     * @throws NotFoundHttpException
     */
    protected function findSocial($id)
    {
        if (($model = UserSocial::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }

    /**
     * @param $id
     * @return Address|null
     * @throws NotFoundHttpException
     */
    protected function findAddress($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'error.model_not_found'));
    }
}