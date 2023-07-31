<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class MailerDriver
 * @package common\base\enum
 */
class MailerDriver extends BaseEnum
{
    const PHP = 'php';
    const SMTP = 'smtp';
    const MAILGUN = 'mailgun';
    const SPARKPOST = 'sparkpost';
    const OFF = 'off';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::PHP => 'Use PHP\'s mail() function (recommended; works in most cases)',
            self::SMTP => 'Set my own SMTP parameters (for advanced users ONLY)',
            self::MAILGUN => 'Use Mailgun API (for mailgun user ONLY)',
            self::SPARKPOST => 'Use Sparkpost API (for sparkpost user ONLY)',
            self::OFF => 'Never send emails (may be useful for testing purposes)',
        ];
    }
}