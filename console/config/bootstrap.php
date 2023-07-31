<?php
if (defined('YII_DEBUG') && YII_DEBUG) {
    Yii::$container->set('yii\log\Logger', 'common\base\log\Logger');
}