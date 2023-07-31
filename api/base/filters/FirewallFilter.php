<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\filters;

use common\base\traits\RuntimeCache;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\ExitException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HeaderCollection;
use yii\web\Request;
use Yii;

/**
 * Class FirewallFilter
 * @package frontend\base\filters
 */
class FirewallFilter extends ActionFilter
{
    use RuntimeCache;

    public $headerParams = [
        'x-system-name' => '*',
        'x-system-version' => '*',
        'x-device-name' => '*',
    ];

    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }


    /**
     * @param Action $event
     * @return bool
     * @throws ExitException
     */
    public function beforeAction($event)
    {
        if (empty($this->headerParams)) {
            return parent::beforeAction($event);
        }

        $headers = $this->getHeaders();
        foreach ($this->headerParams as $name => $whitelist) {
            $value = isset($headers[$name]) ? $headers[$name] : null;
            if ($value === null) {
                throw new BadRequestHttpException('Invalid Request !');
            }

            if (is_string($whitelist) && $whitelist === '*') {
                continue;
            } elseif (is_array($whitelist) && in_array($value, $whitelist)) {
                continue;
            }

            throw new BadRequestHttpException('Invalid Request !');
        }

        return parent::beforeAction($event);
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
}
