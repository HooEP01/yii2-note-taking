<?php
/**
 * @author RYU Chua <ryu@riipay.my>
 *  @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace backend\forms;

use common\base\helpers\ArrayHelper;
use common\base\helpers\Json;
use common\models\User;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class MfaLoginForm
 * @package backend\forms
 */
class MfaLoginForm extends LoginForm
{
    public $otp;

    /**
     * @var string
     */
    private $_data;

    /**
     * @var array
     */
    private $_rawData;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'otp'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool|User whether the user is logged in successfully
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function login()
    {
        $data = $this->getRawData();
        Yii::error($data);
        $this->username = ArrayHelper::getValue($data, 'u');
        $this->password = ArrayHelper::getValue($data, 'p');
        $this->rememberMe = (bool) ArrayHelper::getValue($data, 'r');

        if ($this->validate()) {
            $user = $this->getUser();
            if ($user === null) {
                return false;
            }

            if (empty($user->authMfaToken)) {
                $user->resetAuthMfaToken();
            }

            $otp = $user->getTimeBaseOTP();
            if ($otp->verify($this->otp, null, 1)) {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 12 * 1 : $this->duration);
            } else {
                $this->addError('otp', 'Invalid OTP');
            }
        }

        return false;
    }

    /**
     * #return bool
     */
    public function getIsDataValid()
    {
        try {
            $data = $this->getRawData();

            if (($username = ArrayHelper::getValue($data, 'u')) === null || empty($username)) {
                return false;
            }

            if (($password = ArrayHelper::getValue($data, 'p')) === null || empty($password)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return false;
    }

    /**
     * @return array
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    protected function getRawData()
    {
        if (isset($this->_rawData)) {
            return $this->_rawData;
        }

        return $this->_rawData = Yii::$app->openssl->decryptParams($this->_data);
    }

    /**
     * @param $value
     */
    protected function setData($value)
    {
        $this->_data = $value;
    }
}