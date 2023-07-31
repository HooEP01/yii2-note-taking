<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero Sdn Bhd
 */

namespace common\base\services;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\Result;
use Bego\Database;
use yii\base\BaseObject;
use Yii;

/**
 * Class DynamoDb
 * @property Database $database
 * @property DynamoDbClient $client
 * @property Marshaler $marshaler
 * @package icares\services
 */
class DynamoDb extends BaseObject
{
    /**
     * @var string
     */
    public $tablePrefix = 'Dev-';

    /**
     * @var Database
     */
    private $_database;
    /**
     * @var DynamoDbClient
     */
    private $_client;

    /**
     * @param array $data
     * @return array
     */
    protected function marshalItem($data)
    {
        $helper = $this->getMarshaler();
        return $helper->marshalItem($data);
    }

    /**
     * @param $tableName
     * @param array $options
     * @return Result
     */
    public function query($tableName, $options = [])
    {
        $options['TableName'] = $this->generateTableName($tableName);
        return $this->client->query($options);
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     */
    public function createItem($tableName, $data)
    {
        try {
            $this->client->putItem([
                'TableName' => $this->generateTableName($tableName),
                'Item' => $this->marshalItem($data),
            ]);

            return true;
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return false;
    }

    public function getItem($tableName, $key)
    {
        return $this->client->getItem([
            'Key' => $this->marshalItem($key),
            'TableName' => $this->generateTableName($tableName),
        ]);
//        var_dump($result);
//
//        try {
//
//        } catch (\Exception $e) {
//            Yii::error($e);
//        }

//        return false;

        /**
         * $result = $client->getItem([
        'AttributesToGet' => ['<string>', ...],
        'ConsistentRead' => true || false,
        'ExpressionAttributeNames' => ['<string>', ...],
        'Key' => [ // REQUIRED
        '<AttributeName>' => [
        'B' => <string || resource || Psr\Http\Message\StreamInterface>,
        'BOOL' => true || false,
        'BS' => [<string || resource || Psr\Http\Message\StreamInterface>, ...],
        'L' => [
        [...], // RECURSIVE
        // ...
        ],
        'M' => [
        '<AttributeName>' => [...], // RECURSIVE
        // ...
        ],
        'N' => '<string>',
        'NS' => ['<string>', ...],
        'NULL' => true || false,
        'S' => '<string>',
        'SS' => ['<string>', ...],
        ],
        // ...
        ],
        'ProjectionExpression' => '<string>',
        'ReturnConsumedCapacity' => 'INDEXES|TOTAL|NONE',
        'TableName' => '<string>', // REQUIRED
        ]);
         */
    }

    /**
     * @param $name
     * @return string
     */
    public function generateTableName($name)
    {
        return sprintf('%s%s', $this->tablePrefix, $name);
    }

    /**
     * @param string|array $className
     * @return \Bego\Table
     * @throws \yii\base\InvalidConfigException
     */
    public function table($className)
    {
        $model = Yii::createObject($className);
        return $this->database->table($model);
    }

    /**
     * @return Marshaler
     */
    public function getMarshaler()
    {
        return new Marshaler(['ignore_invalid' => true]);
    }

    /**
     * @return Database
     */
    public function getDatabase()
    {
        if (isset($this->_database)) {
            return $this->_database;
        }

        return $this->_database = new Database($this->client, $this->marshaler);
    }

    /**
     * @return DynamoDbClient
     */
    public function getClient()
    {
        if (isset($this->_client)) {
            return $this->_client;
        }

        return Yii::$app->aws->getDynamoDbClient();
    }
}
