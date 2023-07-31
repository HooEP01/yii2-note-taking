<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\services;

use Aws\Rekognition\RekognitionClient;
use Aws\CloudWatch\CloudWatchClient;
use Aws\CodeDeploy\CodeDeployClient;
use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Rds\RdsClient;
use Aws\S3\S3Client;
use Aws\Sqs\SqsClient;
use common\base\traits\RuntimeCache;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class Aws
 * @property Credentials credentials
 * @package common\base\services
 */
class Aws extends BaseObject
{
    use RuntimeCache;

    /**
     * @var string default region
     */
    public $region;
    /**
     * @var bool
     */
    public $useEnvironmentCredential = false;
    /**
     * the API access key
     * @var string
     */
    private $_apiKey;
    /**BaseObject
     * the API access secret
     * @var string
     */
    private $_apiSecret;

    /**
     * @param $value string
     */
    protected function setApiKey($value)
    {
        $this->_apiKey = $value;
    }

    /**
     * @param $value string
     */
    protected function setApiSecret($value)
    {
        $this->_apiSecret = $value;
    }

    /**
     * @param array $config
     * @return S3Client
     */
    public function getS3Client($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new S3Client($options);
        });
    }

    /**
     * @param array $config
     * @return RdsClient
     */
    public function getRdsClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new RdsClient($options);
        });
    }

    /**
     * @param array $config
     * @return DynamoDbClient
     */
    public function getDynamoDbClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new DynamoDbClient($options);
        });
    }

    /**
     * @param array $config
     * @return SqsClient
     */
    public function getSqsClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new SqsClient($options);
        });
    }

    /**
     * @param array $config
     * @return RekognitionClient
     */
    public function getRekognitionClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new RekognitionClient($options);
        });
    }

    /**
     * @param array $config
     * @return CodeDeployClient
     */
    public function getCodeDeployClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new CodeDeployClient($options);
        });
    }

    /**
     * @param array $config
     * @return CloudWatchClient
     */
    public function getCloudWatchClient($config = [])
    {
        $key = md5(__METHOD__ . '-v1-' . @serialize($config));
        return $this->getOrSetRuntimeData($key, function () use ($config) {
            $options = $this->generateOptions($config);
            return new CloudWatchClient($options);
        });
    }

    /**
     * @return Credentials
     */
    public function getCredentials()
    {
        $credentials = new Credentials($this->_apiKey, $this->_apiSecret);
        return $credentials;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function generateOptions($config = [])
    {
        $defaultOptions = [
            'version' => 'latest',
            'region' => $this->region,
        ];

        if (!$this->useEnvironmentCredential) {
            $defaultOptions['credentials'] = $this->getCredentials();
        }

        $options = ArrayHelper::merge($defaultOptions, $config);
        return $options;
    }
}
