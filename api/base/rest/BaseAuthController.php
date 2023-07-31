<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use common\models\User;
use Yii;

/**
 * Class BaseAuthController
 * @package api\base\rest
 */
abstract class BaseAuthController extends Controller
{
    /**
     * @param User $user
     * @param boolean $isSignUp
     * @return array
     */
    protected function responseLoginSuccess($user, $isSignUp = false)
    {
        $token = Yii::$app->jwt->issueToken(['sub' => $user->id, 'jti' => $user->authKey]);
        return $this->createResponse()
            ->addSuccessMessage(Yii::t('api', 'auth.login_success_message'))
            ->addExtraData('token', $token->toString())
            ->addExtraData('attribute', ['isSignUp' => $isSignUp])
            ->addExtraData('user', $user->toArray($this->loadFields(), $this->loadExpand()))
            ->success();
    }
}
