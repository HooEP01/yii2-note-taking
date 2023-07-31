<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\filters;

use common\base\helpers\Json;
use common\base\helpers\UuidHelper;
use common\base\services\Platform;
use common\jobs\TrackUserActivity;
use common\models\User;
use yii\base\ActionEvent;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\Request;
use Yii;

/**
 * Class ActivityTracking
 * @property User $user
 * @package api\base\filters
 */
class ActivityTracking extends ActionFilter
{
    /**
     * @var string|Platform
     */
    public $platform = 'platform';
    /**
     * @var User
     */
    private $_user;

    public function init()
    {
        parent::init();
        $this->platform = Instance::ensure($this->platform, Platform::class);
    }

    /**
     * @inheritdoc
     * @param ActionEvent $event
     */
    public function beforeFilter($event)
    {
        if (Yii::$app->user->getIsGuest()) {
            Yii::debug(__CLASS__ . ' Guest - Skipped !');
            return true;
        }

        Yii::beginProfile(__CLASS__, 'activity');
        $this->updateUserActivity();
        Yii::endProfile(__CLASS__, 'activity');

        return true;
    }

    /**
     * update the user device
     */
    protected function updateUserActivity()
    {
        if ($this->user === null) {
            return;
        }

        try {
            $attributes = $this->platform->getActivityProfile();
            $attributes['pk'] = UuidHelper::uuid();
            $attributes['userId'] = $this->user->id;

            if (($request = Yii::$app->request) instanceof Request) {
                $attributes['request'] = Json::encode([
                    'serverName' => $request->getServerName(),
                    'serverPort' => $request->getServerPort(),
                    'port' => $request->getPort(),
                    'method' => $request->getMethod(),
                    'path' => $request->getPathInfo(),
                    'absoluteUrl' => $request->getAbsoluteUrl(),
                    'baseUrl' => $request->getBaseUrl(),
                    'contentType' => $request->getContentType(),
                    'headerParams' => $request->getHeaders(),
                    'bodyParams' => $request->getBodyParams(),
                    'queryParams' => $request->getQueryParams(),
                    'serverParams' => $_SERVER,
                ]);
            }

            Yii::$app->pipeline->push(new TrackUserActivity(['id' => $this->user->id, 'attributes' => $attributes]));
        } catch (\Exception $e) {
            Yii::error($e, 'ActivityTracking');
        }
    }

    /**
     * @return User|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        if (isset($this->_user)) {
            return $this->_user;
        }

        if (Yii::$app->user->getIsGuest()) {
            return null;
        }

        return $this->_user = Yii::$app->user->getIdentity();
    }
}
