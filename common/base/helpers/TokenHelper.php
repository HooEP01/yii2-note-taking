<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use yii\base\BaseObject;
use yii\base\InvalidValueException;
use yii\helpers\Json;
use Yii;

/**
 * Class TokenHelper
 * @package common\base\helpers
 */
class TokenHelper extends BaseObject
{
    /**
     * @var string the hashing key for generating token
     */
    public $hashKey = 'default-dummy-hash-key-by-ryu';

    /**
     * resolve the token into payload data
     * if invalid token, false will be returl
     *
     * @param $token
     * @return bool|array
     */
    public function read($token)
    {
        if ($this->verify($token)) {
            list($payload, ) = explode('.', $token);
            $json = $this->decode($payload);
            return Json::decode($json);
        }
        return false;
    }

    /**
     * generate the web token based on data pass in
     *
     * @param array $data
     * @return string
     */
    public function generate($data)
    {
        if (is_array($data)) {
            $json = Json::encode($data);
            $payload = $this->encode($json);
            $signature = $this->hash($payload);

            return $payload . '.' . $signature;
        } else {
            throw new InvalidValueException('$data must be an array !');
        }
    }

    /**
     * verify if the token valid
     * A valid token should separate by a dot
     * the first portion is the payload, and the second is the signature
     *
     * @param $token
     * @return bool
     */
    public function verify($token)
    {
        if (strpos($token, '.') !== false) {
            list($payload, $signature) = explode('.', $token);
            if ($payload !== null && $signature !== null) {
                $expected = $this->hash($payload);
                return $signature == $expected;
            }
        }
        return false;
    }

    /**
     * generate the hash string
     * @param $data
     * @return string
     */
    protected function hash($data)
    {
        return hash_hmac('sha256', $data, $this->hashKey);
    }

    /**
     * @param string $data
     * @return string
     */
    public function encode($data)
    {
        return base64_encode($data);
    }

    /**
     * @param string $data
     * @return mixed|array
     */
    public function decode($data)
    {
        return base64_decode($data);
    }

    /**
     * @return TokenHelper|static
     * @throws \yii\base\InvalidConfigException
     */
    public static function factory()
    {
        $hashKey = Yii::$app->params['user.password.token.hash'];
        return Yii::createObject(['class' => TokenHelper::class, 'hashKey' => $hashKey]);
    }
}
