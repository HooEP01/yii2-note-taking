<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\filters;

use api\base\web\User;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * Class QueryParamAuth
 * @package api\base\filters
 */
class QueryParamAuth extends \yii\filters\auth\QueryParamAuth
{
    /**
     * @param User $user
     * @param Request $request
     * @param Response $response
     * @return \yii\web\IdentityInterface|null
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Invalid Token');
    }
}
