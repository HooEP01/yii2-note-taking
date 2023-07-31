<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\console;

use common\base\DateTime;
use yii\console\controllers\HelpController;
use yii\helpers\Console;

/**
 * Class Controller
 * @package common\base\console
 */
class Controller extends \yii\console\Controller
{
    /**
     * Print this command's help
     * @throws \yii\console\Exception
     */
    public function actionHelp()
    {
        $cmd = new HelpController('help', null);
        $cmd->actionIndex($this->getUniqueId());
    }

    /**
     * @param string|mixed $text
     */
    protected function info($text)
    {
        echo $this->printTimestampText($text, Console::FG_BLUE);
    }

    /**
     * @param string|mixed $text
     */
    protected function success($text)
    {
        echo $this->printTimestampText($text, Console::FG_GREEN);
    }

    /**
     * @param string|mixed $text
     */
    protected function warning($text)
    {
        echo $this->printTimestampText($text, Console::FG_YELLOW);
    }

    /**
     * @param string|mixed $text
     */
    protected function error($text)
    {
        echo $this->printTimestampText($text, Console::FG_RED);
    }

    /**
     * @param string|mixed $text
     */
    protected function cyan($text)
    {
        echo $this->printTimestampText($text, Console::FG_CYAN);
    }

    /**
     * @param string|mixed $text
     */
    protected function purple($text)
    {
        echo $this->printTimestampText($text, Console::FG_PURPLE);
    }

    /**
     * @param string|mixed $text
     * @param int $width potential text width, default to 100
     */
    protected function progress($text, $width = 100)
    {
        echo "\r" . str_repeat(' ', $width);
        echo "\r" . $this->ansiFormat($text, Console::FG_GREY);
    }

    /**
     * @param string|mixed $text
     * @param int $color
     */
    protected function trace($text, $color = Console::FG_GREY)
    {
        if (YII_DEBUG) {
            echo $this->printTimestampText($text, $color);
        }
    }

    /**
     * @param string $text
     * @param int $color
     */
    protected function printTimestampText($text, $color)
    {
        if (is_string($text)) {
            $text = sprintf('%s: %s', DateTime::getCurrentDateTime(), $text);
        } else {
            $text = sprintf('%s: %s', DateTime::getCurrentDateTime(), var_export($text, true));
        }

        echo $this->ansiFormat($text, $color) . "\n";
    }
}