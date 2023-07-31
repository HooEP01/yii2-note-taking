<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace backend\forms;

use common\base\helpers\Json;
use common\models\User;
use Yii;
use yii\base\Exception;

/**
 * Class LoginForm
 * @package backend\forms
 */
class LoginForm extends \common\forms\LoginForm
{
    public $rememberMe = true;
    public $duration = 0;

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool|User whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user === null) {
                return false;
            }

            if (!$user->getIsMfaRequired()) {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 1 : $this->duration);
            }

            return $user;
        }

        return false;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generateMfaAuthData()
    {
        return Yii::$app->openssl->encryptParams([
            'u' => $this->username,
            'p' => $this->password,
            'r' => $this->rememberMe,
        ]);
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        $user = parent::getUser();
        if ($user && $user->getIsAdmin()) {
            return $user;
        }

        return null;
    }
}