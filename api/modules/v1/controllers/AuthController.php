<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\BaseAuthController;
use api\forms\AuthSignInForm;
use common\base\helpers\MobilePhoneHelper;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * Class AuthController
 * @package api\modules\user\controllers
 */
class AuthController extends BaseAuthController
{
    /**
     * @return array
     */
    protected function optionals()
    {
        return [
            'firebase',
            'verify',
        ];
    }

    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'firebase' => ['POST'],
            'verify' => ['POST'],
        ];
    }

    /**
     * @return AuthSignInForm|array
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionFirebase()
    {
        $model = new AuthSignInForm(['scenario' => AuthSignInForm::SCENARIO_FIREBASE]);
        if ($model->load($this->getBodyData(), 'data') && $model->process()) {
            return $this->responseLoginSuccess($model->user, $model->isSignup);
        } elseif ($model->hasErrors()) {
            return $model;
        }

        throw new ForbiddenHttpException($model->errorMessage);
    }

    /**
     * @return array
     */
    public function actionVerify()
    {
        $prefix = Yii::$app->request->getBodyParam('prefix');
        $number = Yii::$app->request->getBodyParam('number');

        return MobilePhoneHelper::resolve($prefix . $number);
    }
}