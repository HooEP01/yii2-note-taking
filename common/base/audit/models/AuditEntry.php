<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://propertygenie.my
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\audit\models;

use common\base\db\ActiveRecord;
use yii\console\Request as ConsoleRequest;
use yii\web\Application as WebApplication;
use yii\web\Request as WebRequest;
use Yii;

/**
 * Class AuditEntry
 * @package common\base\audit
 *
 * @property int $id
 * @property int $userId
 * @property double $duration
 * @property string $ipAddress
 * @property string $requestMethod
 * @property string $requestRoute
 * @property bool $isAjax
 * @property int $memoryMax
 * @property string $createdAt
 *
 * @property AuditTrail[] $trails
 */
class AuditEntry extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audit_entry}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['userId'], 'string', 'max' => 36],
            [['duration'], 'number'],
            [['maxMemory'], 'integer'],
            [['requestMethod', 'requestRoute', 'ipAddress'], 'safe'],
            [['isAjax'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'duration' => 'Duration',
            'ipAddress' => 'Ip Address',
            'requestMethod' => 'Request Method',
            'requestRoute' => 'Route',
            'isAjax' => 'Ajax',
            'maxMemory' => 'Memory Max',
            'createdAt' => 'Created At',
        ];
    }

    /**
     * @return static
     */
    public static function create()
    {
        $entry = new static;
        $entry->record();

        return $entry;
    }

    /**
     * Records the current application state into the instance.
     */
    public function record()
    {
        $app = Yii::$app;
        $request = $app->request;
        $this->requestRoute = $app->requestedAction ? $app->requestedAction->uniqueId : null;

        if ($request instanceof WebRequest) {
            $this->userId = (Yii::$app instanceof WebApplication && Yii::$app->user) ? Yii::$app->user->id : null;
            $this->ipAddress = Yii::$app->platform->getIpAddress();
            $this->isAjax = $request->isAjax;
            $this->isAjax = $request->getIsAjax();
            $this->requestMethod = $request->method;
        } elseif ($request instanceof ConsoleRequest) {
            $this->requestMethod = 'CLI';
        }
        $this->save(false);
    }

    /**
     * @return bool
     */
    public function finalize()
    {
        $app = Yii::$app;
        $request = $app->request;
        if (!$this->userId && $request instanceof WebRequest) {
            $this->userId = Yii::$app->audit->userId;
        }
        $this->duration = microtime(true) - YII_BEGIN_TIME;
        $this->memoryMax = memory_get_peak_usage();
        return $this->save(false, ['duration', 'memoryMax', 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrails()
    {
        return $this->hasMany(AuditTrail::class, ['entryId' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AuditEntryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuditEntryQuery(get_called_class());
    }
}
