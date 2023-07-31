<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\filters;

use Yii;

/**
 * Class VerbFilter
 * @package frontend\base\filters
 */
class VerbFilter extends \yii\filters\VerbFilter
{
    /**
     * @inheritdoc
     * @param \yii\base\ActionEvent $event
     */
    public function beforeAction($event)
    {
        $verb = Yii::$app->getRequest()->getMethod();
        if (strtoupper($verb) == 'OPTIONS') {
            $action = $event->action->id;
            if (isset($this->actions[$action])) {
                $verbs = $this->actions[$action];
            } elseif (isset($this->actions['*'])) {
                $verbs = $this->actions['*'];
            } else {
                $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
            }
            $allowed = array_map('strtoupper', $verbs);
            Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $allowed));
            Yii::$app->end();
        }

        return parent::beforeAction($event);
    }
}
