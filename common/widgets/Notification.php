<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\widgets;

use common\base\helpers\Json;
use yii\base\Widget;
use yii\web\View;
use Yii;

/**
 * Class Notification
 * @package common\widgets
 */
class Notification extends Widget
{
    /**
     * @return array
     */
    protected function getTitles()
    {
        return [
            'error' => 'Error !',
            'danger' => 'Error !',
            'success' => 'Success !',
            'info' => 'Info !',
            'warning' => 'Warning !',
        ];
    }

    protected function registerToastrOptions()
    {
        $options = [
            'closeButton' => true,
            'newestOnTop' => true,
            'progressBar' => true,
            'positionClass' => 'toast-top-right',
            'showDuration' => 300,
            'hideDuration' => 1000,
            'timeOut' => 5000,
            'extendedTimeOut' => 1000,
            'showEasing' => 'swing',
            'hideEasing' => 'linear',
            'showMethod' => 'fadeIn',
            'hideMethod' => 'fadeOut',
        ];

        $js = 'toastr.options = ' . Json::encode($options) . ';';
        $this->view->registerJs($js, View::POS_READY, 'ToastrGlobalOptions');
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        $titles = $this->getTitles();

        $this->registerToastrOptions();

        foreach ($flashes as $type => $flash) {
            if (!isset($titles[$type])) {
                continue;
            }

            foreach ((array) $flash as $i => $message) {
                if (in_array($type, ['error', 'critical'])) {
                    $js = sprintf("toastr.%s(%s, '%s', %s);", $type, json_encode($message), $titles[$type], Json::encode(['timeOut' => 30000]));
                } else {
                    $js = sprintf("toastr.%s(%s, '%s');", $type, json_encode($message), $titles[$type]);
                }
                $this->view->registerJs('setTimeout(function () {' . $js . '}, ' . '200' . ');');
            }

            $session->removeFlash($type);
        }
    }
}
