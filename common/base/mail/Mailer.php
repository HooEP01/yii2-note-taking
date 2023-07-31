<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\mail;

use common\base\enum\MailerDriver;
use common\base\enum\ConfigName;
use common\base\services\Config;
use yii\di\Instance;
use yii\mail\BaseMailer;
use yii\mail\MessageInterface;
use Yii;

/**
 * Class Mailer
 * @package common\base\mail
 */
class Mailer extends BaseMailer
{
    /**
    * @var string|bool
    */
    public $htmlLayout =  false;

    /**
    * @var string|bool
    */
    public $textLayout = false;

    /**
    * @var bool debug mode or not
    */
    public $debugMode = false;

    /**
    * @var string the debug mode delivery to
    */
    public $debugEmail = ['me@ryu.my' => 'Hustle Hero Developers'];

    /**
    * @var string|array|Config
    */
    public $config = 'config';

    /**
    * the actual mailer
    * @var BaseMailer
    */
    private $__mailer;

    /**
    *
    */
    public function init()
    {
        parent::init();

        $this->config = Instance::ensure($this->config, Config::class);
    }

    /**
    * @inheritdoc
    */
    public function compose($view = null, array $params = [])
    {
        $message = $this->createMessage();
        if ($view === null) {
          return $message;
        }

        $message->setView($view);
        $message->setParams($params);

        return $message;
    }

    /**
    * @param MessageInterface $message
    * @return bool
    */
    public function send($message)
    {
        if (YII_DEBUG || $this->debugMode) {
          $message->setTo($this->debugEmail);
          $message->setCc([]);
          $message->setBcc([]);
        }

        return parent::send($message);
    }

    /**
    * @return BaseMailer
    */
    protected function getMailer()
    {
        if (isset($this->__mailer)) {
          return $this->__mailer;
        }

        $this->__mailer = $this->getDriverConfig();
        $this->__mailer = Instance::ensure($this->__mailer, BaseMailer::class);

        return $this->__mailer;
    }

    /**
     * @return array
     */
    protected function getDriverConfig()
    {
        $driver = $this->config->get(ConfigName::MAILER_DRIVER, MailerDriver::OFF);

        if ($driver === MailerDriver::PHP) {
            return $this->getPhpMailerConfig();
        }

        if ($driver === MailerDriver::SMTP) {
            return $this->getSmtpMailerConfig();
        }

        if ($driver === MailerDriver::MAILGUN) {
            return $this->getMailgunMailerConfig();
        }

        if ($driver === MailerDriver::SPARKPOST) {
            return $this->getSparkpostMailerConfig();
        }

        //default, off
        return [
          'class' => 'yii\swiftmailer\Mailer',
          'useFileTransport' => true,
        ];
    }

    /**
     * @return array
     */
    protected function getPhpMailerConfig()
    {
        return [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => YII_DEBUG,
            'messageConfig' => $this->getMessageConfig(),
        ];
    }

    /**
     * @return array
     */
    protected function getSmtpMailerConfig()
    {
        $server = $this->config->get(ConfigName::MAILER_SMTP_SERVER, 'localhost');
        $username = $this->config->get(ConfigName::MAILER_SMTP_USERNAME, 'user');
        $password = $this->config->get(ConfigName::MAILER_SMTP_PASSWORD, 'password');
        $encryption = $this->config->get(ConfigName::MAILER_SMTP_ENCRYPTION, '');
        $port = $this->config->get(ConfigName::MAILER_SMTP_PORT, '25');

        return [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => YII_DEBUG,
            'messageConfig' => $this->getMessageConfig(),
            'transport' => [
              'class' => 'Swift_SmtpTransport',
              'host' => $server,
              'username' => $username,
              'password' => $password,
              'port' => $port,
              'encryption' => $encryption,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getMailgunMailerConfig()
    {
        $domain = $this->config->get(ConfigName::MAILER_MAILGUN_DOMAIN, 'mg.example.com');
        $key = $this->config->get(ConfigName::MAILER_MAILGUN_KEY, 'mailgun-api-key');

        return [
            'class' => 'common\base\mail\mailgun\Mailer',
            'messageConfig' => $this->getMessageConfig(),
            'key' => $key,
            'domain' => $domain,
        ];
    }

    /**
     * @return array
     */
    protected function getSparkpostMailerConfig()
    {
        $baseUrl = $this->config->get(ConfigName::MAILER_SPARKPOST_BASE_URL, 'https://api.sparkpost.com/api/v1');
        $apiKey = $this->config->get(ConfigName::MAILER_SPARKPOST_API_KEY, '-api-key-');

        return [
            'class' => 'common\base\mail\sparkpost\Mailer',
            'messageConfig' => $this->getMessageConfig(),
            'baseUrl' => $baseUrl,
            'apiKey' => $apiKey,
        ];
    }

    /**
     * @return array
     */
    protected function getMessageConfig()
    {
        $senderName = $this->config->get(ConfigName::MAILER_SENDER_NAME);
        $senderEmail = $this->config->get(ConfigName::MAILER_SENDER_EMAIL);

        if (empty($senderEmail)) {
            $senderEmail = 'noreply@example.com';
        }

        if (empty($senderName)) {
            $senderName = $senderEmail;
        }

        return [
            'charset' => 'UTF-8',
            'from' => [$senderEmail => $senderName],
        ];
    }

    /**
     * @return Message
     */
    protected function createMessage()
    {
        $messageConfig = $this->messageConfig;
        if (!array_key_exists('class', $messageConfig)) {
            $messageConfig['class'] = $this->getMailer()->messageClass;
        }

        $messageConfig['mailer'] = $this->__mailer;

        $config = [
            'class' => 'common\base\mail\Message',
            'message' => $messageConfig,
            'mailer' => $this,
        ];

        /** @var Message $message */
        $message = Yii::createObject($config);

        return $message;
    }

    /**
     * Sends the specified message.
     * This method should be implemented by child classes with the actual email sending logic.
     * @param MessageInterface $message the message to be sent
     * @return bool whether the message is sent successfully
     */
    protected function sendMessage($message)
    {
        return $this->getMailer()->sendMessage($message);
    }
}
