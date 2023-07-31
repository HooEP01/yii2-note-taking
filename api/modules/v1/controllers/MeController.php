<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\controllers;

use api\base\rest\Controller;
use api\forms\AvatarForm;
use api\forms\IdentityBindForm;
use api\models\Profile;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use Yii;

/**
 * Class MeController
 * @package api\modules\v1\controllers
 */
class MeController extends Controller
{
    /**
     * @return array
     */
    protected function verbs()
    {
        return [
            'profile' => ['GET'],
            'profile-update' => ['PUT'],
            'avatar' => ['PUT'],
            'bind' => ['POST'],
        ];
    }

    /**
     * @return Profile|null
     * @throws NotFoundHttpException
     */
    public function actionProfile()
    {
        return $this->getUserProfile();
    }

    /**
     * @return Profile|array|null
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionProfileUpdate()
    {
        $profile = $this->getUserProfile();
        $profile->setScenario('profile-update');

        if ($profile->load($this->getBodyData(), 'data') && $profile->save()) {
            return $this->createResponse()
                ->addSuccessMessage(Yii::t('api', 'me.profile_update_success_message'))
                ->addExtraData('profile', $profile)
                ->success();
        } elseif ($profile->hasErrors()) {
            return $profile;
        }

        throw new ServerErrorHttpException('Unknown Server Error !');
    }

    /**
     * @api {put} /me/avatar Update Avatar
     * @apiGroup Profile
     * @apiName PUT_ME_AVATAR
     * @apiDescription Change the profile image / avatar
     * @apiVersion 1.0.0
     *
     * @apiParam {String="image/jpg", "image/jpeg", "image/png", "text/url"} mime The mime of the file uploaded
     * @apiParam {String} content The base64 string of the image
     * @apiParamExample {json} Example
     * {
     *     "mime": "image/png",
     *     "content": "<base64-string>",
     * }
     *
     * @apiPermission authenticated
     * @apiUse AuthHeader
     */
    /**
     * @return AvatarForm|array
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAvatar()
    {
        $model = new AvatarForm(['user' => $this->user]);
        if ($model->load($this->getBodyData(), 'data') && $model->process()) {
            return $this->createResponse()
                ->addSuccessMessage(Yii::t('api', 'me.avatar_success_message'))
                ->addExtraData('profile', $this->user)
                ->success();
        } elseif ($model->hasErrors()) {
            return $model;
        }

        throw new ServerErrorHttpException('Unknown Server Error !');
    }

    /**
     * @return IdentityBindForm|array|null
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBind()
    {
        $model = new IdentityBindForm(['user' => $this->getUserProfile()]);
        if ($model->load($this->getBodyData(), 'data') && $model->process()) {
            return $this->createResponse()
                ->addSuccessMessage(Yii::t('api', 'me.bind_success_message'))
                ->addExtraData('profile', $model->user)
                ->success();
        } elseif ($model->hasErrors()) {
            return $model;
        }

        throw new BadRequestHttpException($model->errorMessage);
    }


        /**
     * @return Profile|null
     * @throws NotFoundHttpException
     */
    protected function getUserProfile()
    {
        if (($model = Profile::findOne($this->user->id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('api', 'model.not_found'));
    }
}