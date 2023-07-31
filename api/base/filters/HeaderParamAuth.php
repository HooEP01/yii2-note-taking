<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\filters;

use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;

/**
 * Class HeaderParamAuth
 * @package api\base\filters
 */
class HeaderParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'x-auth-token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $headers = $request->getHeaders();
        $accessToken = $headers[$this->tokenParam];

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
