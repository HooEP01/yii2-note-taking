<?php

namespace api\modules\v1;

use Yii;

/**
 * customer module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::$app->name = 'Hustle Hero API';
        Yii::$app->setVersion('v1.0.0');
    }
}
