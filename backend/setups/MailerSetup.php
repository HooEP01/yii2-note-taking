<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\setups;

use common\base\enum\ConfigName;
use common\base\enum\MailerDriver;

/**
 * Class MailerSetup
 * @package backend\setups
 */
class MailerSetup extends BaseSetup
{
    public $driver;
    public $senderName;
    public $senderEmail;

    public $smtpDomainName;
    public $smtpServer;
    public $smtpUsername;
    public $smtpPassword;
    public $smtpEncryption;
    public $smtpPort = 25;

    public $mailgunDomain;
    public $mailgunKey;

    public $sparkpostBaseUrl;
    public $sparkpostApiKey;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['driver'], 'in', 'range' => MailerDriver::values()],
            [['senderName'], 'safe'],
            [['senderEmail'], 'email'],
            [['smtpDomainName', 'smtpServer', 'smtpUsername', 'smtpPassword'], 'safe'],
            [['smtpEncryption'], 'in', 'range' => ['none', 'tls', 'ssl']],
            [['smtpPort'], 'integer'],
            [['mailgunDomain', 'mailgunKey'], 'safe'],
            [['sparkpostBaseUrl', 'sparkpostApiKey'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'driver' => 'Mailer Driver',
        ];
    }

    /**
     * @return array
     */
    public function getSmtpEncryptionOptions()
    {
        return [
            'none' => 'None',
            'tls' => 'TLS',
            'ssl' => 'SSL',
        ];
    }

    /**
     * @return array
     */
    protected function getConfigMaps()
    {
        return [
            'driver:string' => ConfigName::MAILER_DRIVER,
            'senderName:string' => ConfigName::MAILER_SENDER_NAME,
            'senderEmail:string' => ConfigName::MAILER_SENDER_EMAIL,
            'smtpDomainName:string' => ConfigName::MAILER_SMTP_DOMAIN,
            'smtpServer:string' => ConfigName::MAILER_SMTP_SERVER,
            'smtpUsername:string' => ConfigName::MAILER_SMTP_USERNAME,
            'smtpPassword:string' => ConfigName::MAILER_SMTP_PASSWORD,
            'smtpEncryption:string' => ConfigName::MAILER_SMTP_ENCRYPTION,
            'smtpPort:integer' => ConfigName::MAILER_SMTP_PORT,
            'mailgunDomain:string' => ConfigName::MAILER_MAILGUN_DOMAIN,
            'mailgunKey:string' => ConfigName::MAILER_MAILGUN_KEY,
            'sparkpostBaseUrl:string' => ConfigName::MAILER_SPARKPOST_BASE_URL,
            'sparkpostApiKey:string' => ConfigName::MAILER_SPARKPOST_API_KEY,
        ];
    }
}