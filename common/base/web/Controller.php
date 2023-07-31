<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\web;

use yii\helpers\Url;
use Yii;

/**
 * Class Controller
 * @property \common\models\User $user
 * @package common\base\web
 */
class Controller extends \yii\web\Controller
{
    /**
     * @param string $name
     */
    protected function rememberUrl($name = 'default')
    {
        $name = $this->getUniqueId() . '.' . $name;
        Url::remember('', $name);
    }

    /**
     * @param string $name
     * @param array $default
     * @return array
     */
    public function getRememberedUrl($name = 'default', $default = ['/'])
    {
        $name = $this->id . '.' . $name;
        if (($url = Url::previous($name)) === null || empty($url)) {
            $url = $default;
        }

        return $url;
    }

    /**
     * @return array
     */
    public function getDefaultListUrl()
    {
        return $this->getRememberedUrl('list', ['list']);
    }

    /**
     * @param string $name
     * @param array|string $default
     * @return \yii\web\Response
     */
    protected function redirectToRememberUrl($name = 'default', $default = ['/'])
    {
        return $this->redirect($this->getRememberedUrl($name, $default));
    }

    /**
     * @return \yii\web\Response
     */
    protected function redirectToDefaultListUrl()
    {
        return $this->redirectToRememberUrl('list', ['list']);
    }

    /**
     * @param array $default
     * @return \yii\web\Response
     */
    protected function redirectToReferrer($default = ['/'])
    {
        return $this->redirect(Yii::$app->request->referrer ?: $default);
    }

    /**
     * @return string
     */
    protected function getActionName()
    {
        return strtolower($this->action->id);
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return sprintf('%s.%s', strtolower($this->id), strtolower($this->action->id));
    }

    /**
     * @return null|\yii\web\IdentityInterface|\common\models\User
     */
    protected function getUser()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity;
        }

        return null;
    }
}
