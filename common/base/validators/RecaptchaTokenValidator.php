<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\validators;

use common\base\helpers\ArrayHelper;
use common\base\helpers\Json;
use yii\httpclient\Client;
use yii\validators\Validator;
use Yii;

/**
 * Class RecaptchaTokenValidator
 * @package common\base\validators
 */
class RecaptchaTokenValidator extends Validator
{
    /**
     * @var string
     */
    public $secret;
    public $url = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('validator', '{attribute} is not a valid recaptcha token.');
        }

        if (!isset($this->secret)) {
            $this->secret = ArrayHelper::getValue(Yii::$app->params, ['recaptcha', 'secret'], '-x-x-');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $client = new Client(['transport' => ['class' => 'yii\httpclient\CurlTransport']]);
        $request = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('POST')
            ->setUrl($this->url)
            ->setData([
                'secret' => $this->secret,
                'response' => $value,
                'remoteip' => Yii::$app->platform->getIpAddress(),
            ]);

        $valid = false;
        $response = $request->send();
        if (($body = $response->getContent()) && Json::validate($body)) {
            $data = Json::decode($body);
            $valid = (bool) ArrayHelper::getValue($data, ['success'], false);
        }

        return $valid ? null : [$this->message, []];
    }
}
