<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\services;

use common\base\helpers\Json;
use common\base\traits\RuntimeCache;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidCallException;
use Yii;

/**
 * Class OpenSSL
 * @package icares\services
 */
class OpenSSL extends BaseObject
{
    use RuntimeCache;

    /**
     * @var string
     */
    public $privateKeyPath;
    /**
     * @var string
     */
    public $publicKeyPath;
    /**
     * @var int|string
     */
    public $signatureAlgorithm;

    /**
     * check and verify
     */
    public function init()
    {
        parent::init();

        if (!isset($this->signatureAlgorithm)) {
            $this->signatureAlgorithm = OPENSSL_ALGO_SHA512;
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function encryptParams($params)
    {
        $json = Json::encode($params);
        $encrypted = $this->privateEncrypt($json);
        return base64_encode($encrypted);
    }

    /**
     * @param string $value
     * @return array
     * @throws Exception
     */
    public function decryptParams($value)
    {
        $encrypted = base64_decode($value);
        $json = $this->publicDecrypt($encrypted);
        return Json::decode($json);
    }

    /**
     * @param mixed $data
     * @return string
     * @throws Exception
     */
    public function privateEncrypt($data)
    {
        $content = '';
        $key = $this->getPrivateKeyContent();

        if (openssl_private_encrypt($data, $content, $key, OPENSSL_PKCS1_PADDING) && !empty($content)) {
            return $content;
        }

        $error = openssl_error_string();
        if (!empty($error)) {
            throw new Exception($error);
        }

        throw new Exception('Unknown Encryption Error !');
    }

    /**
     * @param mixed $data
     * @return string
     * @throws Exception
     */
    public function publicDecrypt($data)
    {
        $content = '';
        $key = $this->getPublicKeyContent();

        if (openssl_public_decrypt($data, $content, $key, OPENSSL_PKCS1_PADDING) && !empty($content)) {
            return $content;
        }

        $error = openssl_error_string();
        if (!empty($error)) {
            throw new Exception($error);
        }

        throw new Exception('Unknown Decryption Error !');
    }

    /**
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function generateSignature($value)
    {
        $signature = '';
        $key = $this->getPrivateKeyContent();

        if (openssl_sign($value, $signature, $key, $this->signatureAlgorithm) && !empty($signature)) {
            return $signature;
        }

        $error = openssl_error_string();
        if (!empty($error)) {
            throw new Exception($error);
        }

        throw new Exception('Unknown Signature Error !');
    }

    /**
     * @param string $value
     * @param mixed $signature
     * @return bool
     */
    public function verifySignature($value, $signature)
    {
        $key = $this->getPublicKeyContent();
        $result = openssl_verify($value, $signature, $key, $this->signatureAlgorithm);
        return $result === 1;
    }

    /**
     * @return mixed|null
     */
    public function getPrivateKeyContent()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (!isset($this->privateKeyPath)) {
                throw new InvalidCallException('$privateKeyPath is not defined !');
            }
            $path = Yii::getAlias($this->privateKeyPath);
            if (file_exists($path) && is_file($path)) {
                return @file_get_contents($path);
            }

            throw new InvalidCallException('Private Key File: "' . $path . '" does not exist !');
        });
    }

    /**
     * @return mixed|null
     */
    public function getPublicKeyContent()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            if (!isset($this->publicKeyPath)) {
                throw new InvalidCallException('$publicKeyPath is not defined !');
            }
            $path = Yii::getAlias($this->publicKeyPath);
            if (file_exists($path) && is_file($path)) {
                return @file_get_contents($path);
            }

            throw new InvalidCallException('Public Key File: "' . $path . '" does not exist !');
        });
    }
}
