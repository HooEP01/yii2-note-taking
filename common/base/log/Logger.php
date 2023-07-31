<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://propertygenie.my
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\log;

use yii\helpers\Console;

/**
 * Class Logger
 * @package common\base\log
 */
class Logger extends \yii\log\Logger
{
    /**
     * @param array|string $message
     * @param int $level
     * @param string $category
     */
    public function log($message, $level, $category = 'application')
    {
        if (defined('YII_CONSOLE_MODE') && YII_CONSOLE_MODE) {
            if ($category === 'application') {
                try {
                    if (!is_string($message)) {
                        $message = var_export($message, true);
                    }

                    $text = sprintf('Console: %s: %s - %s', self::getLevelName($level), $category, $message);
                    if ($level === static::LEVEL_TRACE) {
                        echo Console::ansiFormat($text, [Console::FG_GREEN, Console::ITALIC]) . PHP_EOL;
                    } elseif ($level === static::LEVEL_ERROR) {
                        echo Console::ansiFormat($text, [Console::FG_RED]) . PHP_EOL;
                    } elseif ($level === static::LEVEL_WARNING) {
                        echo Console::ansiFormat($text, [Console::FG_YELLOW]) . PHP_EOL;
                    } elseif ($level === static::LEVEL_INFO) {
                        echo Console::ansiFormat($text, [Console::FG_BLUE]) . PHP_EOL;
                    }
                } catch (\Exception $e) {
                }
            }
        }

        parent::log($message, $level, $category);
    }
}