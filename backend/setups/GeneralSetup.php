<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\setups;

use common\base\enum\ConfigName;
use common\base\enum\OtpProvider;

/**
 * Class GeneralSetup
 * @package backend\setups
 */
class GeneralSetup extends BaseSetup
{
    public $facebookAppId;
    public $facebookAppSecret;

    public $otpProvider = OtpProvider::FIREBASE;

    public $googleMapApiKey;
    public $enforceSuperAdminMultiFactorAuthentication;

    /**
     * @var array
     */
    protected $_rates;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['facebookAppId', 'facebookAppSecret', 'googleMapApiKey'], 'string'],
            [['otpProvider'], 'in', 'range' => OtpProvider::values()],
            [['enforceSuperAdminMultiFactorAuthentication'], 'boolean'],
        ];
    }

    /**
     * @return array
     */
    protected function getConfigMaps()
    {
        return [
            'facebookAppId:string' => ConfigName::FACEBOOK_APP_ID,
            'facebookAppSecret:string' => ConfigName::FACEBOOK_APP_SECRET,

            'otpProvider:string' => ConfigName::OTP_PROVIDER,
            'googleMapApiKey:string' => ConfigName::GOOGLE_MAP_API_KEY,

            'enforceSuperAdminMultiFactorAuthentication:boolean' => ConfigName::ENFORCE_SUPER_ADMIN_MFA,
        ];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if (substr($name, 0, 5) === 'rate_') {
            $group = substr($name, 5);
            return $this->_rates[$group];
        }

        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return mixed|void
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if (substr($name, 0, 5) === 'rate_') {
            $group = str_replace('-', '_', substr($name, 5));
            return $this->_rates[$group] = $value;
        }

        return parent::__set($name, $value);
    }
}