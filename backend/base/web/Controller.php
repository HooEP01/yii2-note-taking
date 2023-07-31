<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\web;

use common\base\enum\LanguageCode;
use common\models\User;
use Yii;

/**
 * Class Controller
 * @property User $user
 * @package backend\base\web
 */
class Controller extends \common\base\web\Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => '\yii\filters\AccessControl',
                'rules' => $this->accessRules(),
            ],
            'contentNegotiator' => [
                'class' => '\yii\filters\ContentNegotiator',
                'languages' => LanguageCode::getSupported(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function goHome()
    {
        $route = ['site/index'];
        if ($this->user !== null) {
            //$route['_lang'] = $this->user->getLanguageCode();
        }

        return Yii::$app->getResponse()->redirect($route);
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $isPopup = (bool) Yii::$app->request->getQueryParam('_popup', 0);
        if ($isPopup) {
            $this->layout = 'popup';
        }

        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = $this->getUniqueId();
        return ucwords($name);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        $language = Yii::$app->request->getQueryParam('language', LanguageCode::ENGLISH);
        return LanguageCode::resolveCode($language);
    }
}
