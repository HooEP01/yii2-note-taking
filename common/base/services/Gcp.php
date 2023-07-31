<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\services;

use common\base\traits\RuntimeCache;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Tasks\V2\CloudTasksClient;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Datastore\DatastoreClient;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\Storage\StorageClient;
use common\base\helpers\Json;
use Psr\Cache\CacheItemPoolInterface;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class Gcp
 * @package common\base\services
 */
class Gcp extends BaseObject
{
    use RuntimeCache;

    /**
     * The project id
     * @var string
     */
    public $projectId;
    /**
     * @var string for if web using
     */
    public $apiKey;
    /**
     * @var string
     */
    public $privateKey;
    /**
     * @var string
     */
    public $privateKeyId;
    /**
     * @var string
     */
    public $clientId;
    /**
     * @var string
     */
    public $clientEmail;
    /**
     * @var string
     */
    public $clientX509CertUrl;
    /**
     * The project id
     * @var string
     */
    public $type = 'service_account';
    /**
     * @var string
     */
    public $authUri = 'https://accounts.google.com/o/oauth2/auth';
    /**
     * @var string
     */
    public $tokenUri = 'https://accounts.google.com/o/oauth2/token';
    /**
     * @var string
     */
    public $authProviderX509CertUrl = 'https://www.googleapis.com/oauth2/v1/certs';
    /**
     * @var string
     */
    public $authCache;
    /**
     * @var bool
     */
    public $keyFilePath;

    /**
     * initializing, make sure have required params
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!isset($this->keyFilePath)) {
            foreach (['projectId', 'privateKey', 'privateKeyId', 'clientId', 'clientEmail', 'clientX509CertUrl'] as $property) {
                if (!isset($this->{$property})) {
                    throw new InvalidConfigException(strtr('{property} must be set !', ['{property}' => $property]));
                }
            }
        }
    }

    /**
     * @return string
     */
    public function generateFirestoreWebInitScript()
    {
        $json = Json::encode($this->getFirestoreWebConfig());
        return "firebase.initializeApp({$json});";
    }

    /**
     * @return array
     */
    public function getFirestoreWebConfig()
    {
        return [
            'apiKey' => $this->apiKey,
            'projectId' => $this->projectId,
        ];
    }

    /**
     * @param array $config
     * @return ImageAnnotatorClient
     * @throws InvalidConfigException
     * @throws \Google\ApiCore\ValidationException
     */
    public function getImageAnnotationClient($config = [])
    {
        $config = ArrayHelper::merge($this->getDefaultConfig(), $config);
        $config['credentials'] = $this->getKeyFile();

        return new ImageAnnotatorClient($config);
    }

    /**
     * @param array $config
     * @return FirestoreClient
     * @throws InvalidConfigException
     */
    public function getFirestoreClient($config = [])
    {
        $key = md5(@serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $config = ArrayHelper::merge($this->getDefaultConfig(), $config);
            return new FirestoreClient($config);
        });
    }

    /**
     * @param array $config
     * @return DatastoreClient
     * @throws InvalidConfigException
     */
    public function getDatastoreClient($config = [])
    {
        $config = ArrayHelper::merge($this->getDefaultConfig(), $config);
        return new DatastoreClient($config);
    }

    /**
     * @param array $config
     * @return StorageClient
     * @throws InvalidConfigException
     */
    public function getStorageClient($config = [])
    {
        $config = ArrayHelper::merge($this->getDefaultConfig(), $config);
        return new StorageClient($config);
    }

    /**
     * @param array $config
     * @return PubSubClient
     * @throws InvalidConfigException
     */
    public function getPubSubClient($config = [])
    {
        $key = md5(__METHOD__ . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $config = ArrayHelper::merge($this->getDefaultConfig(), $config);
            return new PubSubClient($config);
        });
    }

    /**
     * @param array $config
     * @return CloudTasksClient
     * @throws InvalidConfigException
     */
    public function getTaskClient($config = [])
    {
        return new CloudTasksClient(['credentialsConfig' => ['keyFile' => $this->getKeyFile()]]);
    }

    /**
     * @param $id
     * @param array $options
     * @return string
     */
    public function getTaskQueueName($id, $options = [])
    {
        $locationId = ArrayHelper::getValue($options, 'region', 'asia-northeast1');
        return CloudTasksClient::queueName($this->projectId, $locationId, $id);
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    protected function getDefaultConfig()
    {
        $config = [
            'projectId' => $this->projectId,
            'keyFile' => $this->getKeyFile(),
        ];

        if (isset($this->authCache) && ($authCache = Yii::createObject($this->authCache)) instanceof CacheItemPoolInterface) {
            $config['authCache'] = $authCache;
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function getKeyFile()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $keyFile = [
                'type' => $this->type,
                'project_id' => $this->projectId,
                'private_key_id' => $this->privateKeyId,
                'private_key' => $this->privateKey,
                'client_email' => $this->clientEmail,
                'client_id' => $this->clientId,
                'auth_uri' => $this->authUri,
                'token_uri' => $this->tokenUri,
                'auth_provider_x509_cert_url' => $this->authProviderX509CertUrl,
                'client_x509_cert_url' => $this->clientX509CertUrl,
            ];

            if (isset($this->keyFilePath)) {
                $json = @file_get_contents($this->keyFilePath);
                $json = trim($json);

                if (Json::validate($json)) {
                    $keyFile = Json::decode($json);
                }
            }

            return $keyFile;
        });
    }
}
