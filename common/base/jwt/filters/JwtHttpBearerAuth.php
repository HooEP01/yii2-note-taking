<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\jwt\filters;

use common\base\helpers\ArrayHelper;
use common\base\jwt\Jwt;
use common\models\User;
use yii\di\Instance;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use Yii;

/**
 * Class JwtHttpBearerAuth
 * @package common\base\jwt\filters
 */
class JwtHttpBearerAuth extends AuthMethod
{
    /**
     * @var Jwt|string|array the [[Jwt]] object or the application component ID of the [[Jwt]].
     */
    public $jwt = 'jwt';

    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'x-auth-token';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->jwt = Instance::ensure($this->jwt, Jwt::class);
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $headers = $request->getHeaders();
        $accessToken = $headers[$this->tokenParam];

        if (is_string($accessToken) && !empty($accessToken)) {
            $data = $this->jwt->resolveToken($accessToken);

            if (empty($id = ArrayHelper::getValue($data, ['sub']))) {
                return null;
            }
            if (empty($key = ArrayHelper::getValue($data, ['jti']))) {
                return null;
            }

            $identity = User::find()->id($id)->authKey($key)->one();
            if ($identity && $user->login($identity)) {
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