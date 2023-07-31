<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace common\base\services;

use common\base\DateTime;
use common\base\helpers\StringHelper;
use common\base\traits\RuntimeCache;
use Jenssegers\Agent\Agent;
use yii\base\BaseObject;
use yii\web\HeaderCollection;
use yii\web\Request;
use Yii;

/**
 * Class Platform
 * @property string $deviceName
 * @property string $osName
 * @property string $osVersion
 * @property string $browserName
 * @property string $browserVersion
 * @property string $robotName
 * @property bool $isDesktop
 * @property bool $isPhone
 * @property bool $isTablet
 * @property bool $isRobot
 * @package common\base\services
 */
class Platform extends BaseObject
{
    use RuntimeCache;

    public $systemNameParam = 'x-system-name';
    public $systemVersionParam = 'x-system-version';
    public $appVersionParam = 'x-app-version';
    public $deviceModelParam = 'x-device-model';
    public $deviceNameParam = 'x-device-name';
    public $uniqueIdParam = 'x-unique-id';
    public $playerIdParam = 'x-player-id';

    /**
     * @var mixed
     */
    private $_helper;

    public function getActivityProfile()
    {
        return [
            'playerId' => (string) $this->getPlayerId(),
            'ipAddress' => (string) $this->getIpAddress(),
            'userAgent' => (string) $this->getUserAgent(),
            'deviceType' => (string) $this->getDeviceType(),
            'deviceName' => (string) $this->getDeviceName(),
            'deviceModel' => (string) $this->getDeviceModel(),
            'systemName' => (string) $this->getOsName(),
            'systemVersion' => (string) $this->getOsVersion(),
            'browserName' => (string) $this->getBrowserName(),
            'browserVersion' => (string) $this->getBrowserVersion(),
            'appVersion' => (string) $this->getAppVersion(),
            'robotName' => (string) $this->getRobotName(),
            'isRobot' => (bool) $this->getIsRobot(),
            'isWeb' => (bool) $this->getIsWeb(),
            'isApp' => (bool) $this->getIsApp(),
            'isDesktop' => (bool) $this->getIsDesktop(),
            'isPhone' => (bool) $this->getIsPhone(),
            'isAndroid' => (bool) $this->getIsAndroid(),
            'isIos' => (bool) $this->getIsIos(),
            'timestamp' => (int) DateTime::getCurrentTimestamp(),
            'createdAt' => (int) DateTime::getCurrentMicroTime(),
            'updatedAt' => (int) DateTime::getCurrentMicroTime(),
        ];
    }

    /**
     * use 3rd party library as helper to find out the data we want
     * @return Agent
     */
    protected function getHelper()
    {
        if (isset($this->_helper)) {
            return $this->_helper;
        }

        return $this->_helper = new Agent();
    }

    /**
     * @return HeaderCollection|array
     */
    protected function getHeaders()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if ($request = $this->getRequest()) {
                return $request->getHeaders();
            }

            return [];
        }, []);
    }

    /**
     * @return Request|bool
     */
    protected function getRequest()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (($request = Yii::$app->request) instanceof Request) {
                return  $request;
            }

            return false;
        }, false);
    }

    /**
     * @return string|null
     */
    public function getIpAddress()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                if (($request = Yii::$app->request) instanceof Request) {
                    $address = $request->getUserIP();
                }
            }

            if (isset($address)) {
                if (strpos($address, ',') !== false) {
                    $items = StringHelper::explodeByComma($address);
                    return current($items);
                }

                return $address;
            }

            return null;
        });
    }

    /**
     * Get the operating system. (Ubuntu, Windows, OS X, ...)
     * @return mixed
     */
    public function getUniqueId()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $headers = $this->getHeaders();
            return isset($headers[$this->uniqueIdParam]) ? $headers[$this->uniqueIdParam] : null;
        });
    }

    /**
     * Get the operating system. (Ubuntu, Windows, OS X, ...)
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $headers = $this->getHeaders();
            return isset($headers[$this->playerIdParam]) ? $headers[$this->playerIdParam] : null;
        });
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->getHelper()->getUserAgent();
    }

    /**
     * Get the device name, if mobile. (iPhone, Nexus, AsusTablet, ...)
     * @return mixed
     */
    public function getDeviceName()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $headers = $this->getHeaders();

            if (isset($headers[$this->deviceNameParam])) {
                return $headers[$this->deviceNameParam];
            }

            return $this->getHelper()->device();
        });
    }

    /**
     * Get the device model, if mobile. (iPhone, Nexus, AsusTablet, ...)
     * @return mixed
     */
    public function getDeviceModel()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $headers = $this->getHeaders();
            return isset($headers[$this->deviceModelParam]) ? $headers[$this->deviceModelParam] : null;
        });
    }

    /**
     * Get the operating system. (Ubuntu, Windows, OS X, ...)
     * @return mixed
     */
    public function getOsName()
    {
        if ($this->getIsApp()) {
            return $this->getAppOsName();
        }

        return $this->getHelper()->platform();
    }

    /**
     * Get the operating system version
     * @return mixed
     */
    public function getOsVersion()
    {
        if ($this->getIsApp()) {
            return $this->getAppOsVersion();
        }

        return $this->getHelper()->version($this->getOsName());
    }

    /**
     * Get the browser name. (Chrome, IE, Safari, Firefox, ...)
     * @return mixed
     */
    public function getBrowserName()
    {
        if ($this->getIsApp()) {
            return 'Riipay ' . $this->getAppOsName();
        }

        return $this->getHelper()->browser();
    }

    /**
     * Get the browser version
     * @return mixed
     */
    public function getBrowserVersion()
    {
        if ($this->getIsApp()) {
            return $this->getAppVersion();
        }

        return $this->getHelper()->version($this->getBrowserName());
    }

    /**
     * Get the robot name. Note: this currently only works for major robots like Google, Facebook, Twitter, Bing, Baidu etc
     * @return mixed
     */
    public function getRobotName()
    {
        return $this->getHelper()->robot();
    }

    /**
     * @return string
     */
    public function getDeviceType()
    {
        if ($this->getIsApp()) {
            return 'app';
        } elseif ($this->getIsDesktop()) {
            return 'desktop';
        } elseif ($this->getIsPhone()) {
            return 'mobile';
        } elseif ($this->getIsTablet()) {
            return 'tablet';
        } elseif ($this->getIsRobot()) {
            return 'robot';
        }
        
        return 'desktop';
    }

    /**
     * Get the device model, if mobile. (iPhone, Nexus, AsusTablet, ...)
     * @return mixed
     */
    public function getRequestedWith()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $key = 'x-requested-with';
            $headers = $this->getHeaders();
            return isset($headers[$key]) ? $headers[$key] : null;
        });
    }

    /**
     * Check if the user is using a desktop device.
     * @return bool
     */
    public function getIsFlutterInAppWebView()
    {
        $with = $this->getRequestedWith();
        return in_array($with, ['my.riipay.app.user']);
    }

    /**
     * Check if the user is using a desktop device.
     * @return bool
     */
    public function getIsDesktop()
    {
        return $this->getHelper()->isDesktop();
    }

    /**
     * Check if the user is using a phone device.
     * @return bool
     */
    public function getIsPhone()
    {
        return $this->getHelper()->isPhone();
    }

    /**
     * Check if the user is using a tablet device.
     * @return bool
     */
    public function getIsTablet()
    {
        return $this->getHelper()->isTablet();
    }

    /**
     * Check if the user is a robot.
     * @return bool
     */
    public function getIsRobot()
    {
        return $this->getHelper()->isRobot();
    }

    /**
     * @return bool
     */
    public function getIsWeb()
    {
        return !$this->getIsApp();
    }

    /**
     * check if the request from app
     * @return bool
     */
    public function getIsApp()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (($osName = $this->getAppOsName()) === null) {
                return false;
            }

            $osName = strtolower($osName);
            if (!in_array($osName, ['android', 'ios'])) {
                return false;
            }

            if ($this->getAppVersion() === null) {
                return false;
            }

            return true;
        });
    }

    /**
     * @return bool
     */
    public function getIsIos()
    {
        return $this->getIsApp() && $this->getOsName() == 'iOS';
    }

    /**
     * @return bool
     */
    public function getIsAndroid()
    {
        return $this->getIsApp() && $this->getOsName() == 'Android';
    }

    /**
     * @param string|int $version
     * @return bool
     */
    public function getIsAppVersionGreaterThan($version)
    {
        $version = StringHelper::resolveVersion($version);
        $appVersion = StringHelper::resolveVersion($this->getAppVersion());

        return $appVersion > $version;
    }

    /**
     * @param string|int $version
     * @return bool
     */
    public function getIsAppVersionGreaterOrEqualThan($version)
    {
        $version = StringHelper::resolveVersion($version);
        $appVersion = StringHelper::resolveVersion($this->getAppVersion());

        return $appVersion >= $version;
    }

    /**
     * @param string|int $version
     * @return bool
     */
    public function getIsAppVersionLesserThan($version)
    {
        $version = StringHelper::resolveVersion($version);
        $appVersion = StringHelper::resolveVersion($this->getAppVersion());

        return $appVersion < $version;
    }

    /**
     * @param string|int $version
     * @return bool
     */
    public function getIsAppVersionLesserOrEqualThan($version)
    {
        $version = StringHelper::resolveVersion($version);
        $appVersion = StringHelper::resolveVersion($this->getAppVersion());

        return $appVersion <= $version;
    }

    /**
     * @return string
     */
    public function getAppVersion()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $headers = $this->getHeaders();
            return isset($headers[$this->appVersionParam]) ? $headers[$this->appVersionParam] : null;
        });
    }

    /**
     * @return mixed|null|string
     */
    public function getAppOsName()
    {
        return $this->getOrSetRuntimeData(__METHOD__ . '-v1', function () {
            $headers = $this->getHeaders();
            $systemName = isset($headers[$this->systemNameParam]) ? $headers[$this->systemNameParam] : null;

            if (is_scalar($systemName)) {
                $systemName = strtolower($systemName);
                if ($systemName === 'android') {
                    return 'Android';
                } elseif ($systemName === 'ios' || $systemName === 'iphone os') {
                    return 'iOS';
                }

                return ucwords($systemName);
            }

            return $systemName;
        });
    }

    /**
     * @return mixed|null|string
     */
    public function getAppOsVersion()
    {
        return $this->getOrSetRuntimeData(__METHOD__ . '-v1', function () {
            $headers = $this->getHeaders();
            return isset($headers[$this->systemVersionParam]) ? $headers[$this->systemVersionParam] : null;
        });
    }
}
