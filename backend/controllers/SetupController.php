<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;


use backend\base\web\Controller;
use backend\base\web\SetupAction;
use backend\setups\GeneralSetup;
use backend\setups\MailerSetup;

/**
 * Class SetupController
 * @package backend\controllers
 */
class SetupController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'general' => [
                'class' => SetupAction::class,
                'setup' => ['class' => GeneralSetup::class],
                'mainView' => '/setup/main',
                'setupView' => '/setup/general',
            ],
            'mailer' => [
                'class' => SetupAction::class,
                'setup' => ['class' => MailerSetup::class],
                'mainView' => '/setup/main',
                'setupView' => '/setup/mailer',
            ],
        ];
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect(['general']);
    }
}