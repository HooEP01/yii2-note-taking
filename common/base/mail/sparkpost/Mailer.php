<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\mail\sparkpost;

use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\mail\BaseMailer;
use Yii;

/**
 * Class Mailer
 * @package common\mail\sparkpost
 */
class Mailer extends BaseMailer
{
  /**
   * @var string
   */
  public $baseUrl = 'https://api.sparkpost.com/api/v1';


  /**
   * @var string
   */
  public $apiKey;

  /**
   * @var string message default class name.
   */
  public $messageClass = 'common\mail\sparkpost\Message';

  /**
   * initialize
   */
  public function init()
  {
    parent::init();

    if (!isset($this->apiKey)) {
      throw new InvalidConfigException('$apiKey must be set !');
    }

    if (!isset($this->baseUrl)) {
      throw new InvalidConfigException('$baseUrl must be set !');
    }
  }

  /**
   * Sends the specified message.
   * This method should be implemented by child classes with the actual email sending logic.
   * @param Message $message the message to be sent
   * @return bool whether the message is sent successfully
   */
  protected function sendMessage($message)
  {
    Yii::info('Sending email', __METHOD__);

    $params = $message->getMessageParams();

//        if (YII_DEBUG) {
//            $sinkRecipients = [];
//            $recipients = ArrayHelper::getValue($params, 'recipients', []);
//            foreach ($recipients as $recipient) {
//                $recipient['address']['email'] = $recipient['address']['email'] . '.sink.sparkpostmail.com';
//                $sinkRecipients[] = $recipient;
//            }
//            ArrayHelper::setValue($params, 'recipients', $sinkRecipients);
//        }

    if (YII_CONSOLE_MODE) {
      VarDumper::dumpAsString($params);
    }

    $response = $this->createRequest()->setData($params)->send();

    if (YII_CONSOLE_MODE) {
      VarDumper::dumpAsString($response->content);
    }

    Yii::info('Response : ' . var_export($response, true), __METHOD__);
    return true;
  }

  /**
   * @return \yii\httpclient\Request
   * @throws InvalidConfigException
   */
  protected function createRequest()
  {
    return $this->getClient()->createRequest()
      ->setHeaders(['Authorization' => $this->apiKey])
      ->setMethod('POST')
      ->setUrl($this->baseUrl . '/transmissions')
      ->setFormat(Client::FORMAT_JSON);
  }

  /**
   * @return Client
   */
  protected function getClient()
  {
    return new Client(['transport' => [
      'class' => 'yii\httpclient\CurlTransport'
    ]]);
  }
}
