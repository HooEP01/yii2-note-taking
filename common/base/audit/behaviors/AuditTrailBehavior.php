<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://propertygenie.my
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\audit\behaviors;

use common\base\audit\Audit;
use common\base\audit\models\AuditTrail;
use common\base\DateTime;
use common\base\helpers\NanoIdHelper;
use common\base\traits\RuntimeCache;
use ReflectionClass;
use yii\db\ArrayExpression;
use yii\db\BaseActiveRecord;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\Json;
use Yii;

/**
 * Class AuditTrailBehavior
 * @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class AuditTrailBehavior extends Behavior
{
    use RuntimeCache;

    /**
     * @var string|Connection
     */
    public $db = 'db';

    /**
     * @var string|Audit
     */
    public $audit = 'audit';

    /**
     * Array with fields to save
     * You don't need to configure both `allowed` and `ignored`
     * @var array
     */
    public $allowed = [];

    /**
     * Array with fields to ignore
     * You don't need to configure both `allowed` and `ignored`
     * @var array
     */
    public $ignored = [];

    /**
     * Array with tables to ignore
     * @var array
     */
    public $ignored_tables = [];

    /**
     * Timestamp attributes should, in most cases, be ignored. If both AudittrailBehavior and
     * TimestampBehavior logs the created_at and updated_at fields, the data is saved twice.
     * In case you want to log them, you can unset the column from this timestamp column name suggestions.
     * Set to null to disable this filter and log all columns.
     * @var null|array
     */
    public $timestamp_fields = [
        'id', 'userId', 'deeplinkId', 'token',
        'createdAt', 'updatedAt', 'deletedAt',
        'createdBy', 'updatedBy', 'deletedBy',
        'cacheData', 'cacheTranslation',
        'configuration',
    ];

    /**
     * Is the behavior is active or not
     * @var boolean
     */
    public $active = true;

    /**
     * @var array
     */
    private $_oldAttributes = [];

    /**
     * initialize
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
        $this->audit = Instance::ensure($this->audit, Audit::class);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     *
     */
    public function afterFind()
    {
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     *
     */
    public function afterInsert()
    {
        $this->audit('CREATE');
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     *
     */
    public function afterUpdate()
    {
        $this->audit('UPDATE');
        $this->setOldAttributes($this->owner->getAttributes());
    }

    /**
     *
     */
    public function afterDelete()
    {
        $this->audit('DELETE');
        $this->setOldAttributes([]);
    }

    /**
     * @param $action
     * @throws \yii\db\Exception
     */
    public function audit($action)
    {
        // Not active? get out of here
        if (!$this->active) {
            return;
        }

        // check if the tables should be ignore
        if (sizeof($this->ignored_tables) > 0 && $this->owner instanceof ActiveRecord) {
            $tableName = $this->owner->tableName();
            if (in_array($tableName, $this->ignored_tables)) {
                return;
            }
        }

        // If this is a deleted then just write one row and get out of here
        if ($action == 'DELETE') {
            $this->saveAuditTrailDelete();
            return;
        }

        // Now lets actually write the attributes
        $this->auditAttributes($action);
    }

    /**
     * Clean attributes of fields that are not allowed or ignored.
     *
     * @param $attributes
     * @return mixed
     */
    protected function cleanAttributes($attributes)
    {
        $attributes = $this->cleanAttributesAllowed($attributes);
        $attributes = $this->cleanAttributesIgnored($attributes);
        return $this->cleanAttributesSerialize($attributes);
    }

    /**
     * Unset attributes which are not allowed
     *
     * @param $attributes
     * @return mixed
     */
    protected function cleanAttributesAllowed($attributes)
    {
        if (sizeof($this->allowed) > 0) {
            foreach ($attributes as $f => $v) {
                if (!in_array($f, $this->allowed)) {
                    unset($attributes[$f]);
                }
            }
        }
        return $attributes;
    }

    /**
     * Unset attributes which are ignored
     *
     * @param $attributes
     * @return mixed
     */
    protected function cleanAttributesIgnored($attributes)
    {
        if (is_array($this->timestamp_fields) && count($this->timestamp_fields) > 0) {
            $this->ignored = array_merge($this->ignored, $this->timestamp_fields);
        }
        if (count($this->ignored) > 0) {
            foreach ($attributes as $f => $v) {
                if (in_array($f, $this->ignored)) {
                    unset($attributes[$f]);
                }
            }
        }
        return $attributes;
    }

    /**
     * attributes which need to get override with a new value
     *
     * @param $attributes
     * @return mixed
     */
    protected function cleanAttributesSerialize($attributes)
    {
        if (sizeof($attributes) > 0) {
            foreach ($attributes as $field => $value) {
                /** @var ArrayExpression $value */
                if ($value instanceof ArrayExpression) {
                    $attributes[$field] = Json::encode($value->getValue());
                }
            }
        }
        return $attributes;
    }

    /**
     * @param string $action
     * @throws \yii\db\Exception
     */
    protected function auditAttributes($action)
    {
        // Get the new and old attributes
        $newAttributes = $this->cleanAttributes($this->owner->getAttributes());
        $oldAttributes = $this->cleanAttributes($this->getOldAttributes());

        // ensure to handle serialized attributes properly
        foreach ($newAttributes as $key => $value) {
            if (is_array($value)) {
                $newAttributes[$key] = Json::encode($value);
            }
        }

        foreach ($oldAttributes as $key => $value) {
            if (is_array($value)) {
                $oldAttributes[$key] = Json::encode($value);
            }
        }

        // If no difference then get out of here
        if (count(array_diff_assoc($newAttributes, $oldAttributes)) <= 0) {
            return;
        }

        $rows = [];
        $data = $this->generateCommonAuditData($action);
        foreach ($newAttributes as $field => $new) {
            $old = $oldAttributes[$field] ?? '';
            // If they are not the same lets write an audit log
            if ($new != $old) {
                $data['field'] = $field;
                $data['oldValue'] = $old;
                $data['newValue'] = $new;
                $data['traceAt'] = DateTime::getCurrentDateTimeISO8601();
                $rows[] = $data;
            }
        }

        // Record the field changes with a batch insert
        if (!empty($rows)) {
            $columns = [
                'action', 'entryId', 'userId',
                'ownerReference', 'tableName',
                'modelClass', 'modelKey',
                'field', 'oldValue', 'newValue',
                'traceAt',
            ];
            $this->db->createCommand()->batchInsert(AuditTrail::tableName(), $columns, $rows)->execute();
        }
    }

    /**
     * Save the audit trails for a delete action
     */
    protected function saveAuditTrailDelete()
    {
        $row = $this->generateCommonAuditData('DELETE');
        $row['traceAt'] = DateTime::getCurrentDateTimeISO8601();
        $this->db->createCommand()->insert(AuditTrail::tableName(), $row)->execute();
    }

    /**
     * @param string $action
     * @return array
     */
    protected function generateCommonAuditData($action)
    {
        return [
            'action' => $action,
            'entryId' => $this->audit->entry->id,
            'userId' => $this->audit->userId,
            'ownerReference' => $this->getOwnerReference(),
            'tableName' => $this->getTableName(),
            'modelClass' => $this->getClassName(),
            'modelKey' => $this->getOwnerKey(),
        ];
    }

    /**
     * @return array
     */
    public function getOldAttributes()
    {
        return $this->_oldAttributes;
    }

    /**
     * @param $value
     */
    public function setOldAttributes($value)
    {
        $this->_oldAttributes = $value;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->owner->tableName();
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return get_class($this->owner);
    }

    /**
     * @return string
     */
    protected function getOwnerName()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $class = new ReflectionClass($this->owner);
            return $class->getShortName();
        });
    }

    /**
     * @return string
     */
    protected function getOwnerKey()
    {
        return implode(',', $this->owner->getPrimaryKey(true));
    }

    /**
     * @return string
     */
    protected function getOwnerReference()
    {
        return sprintf('SQL#%s#%s', $this->getOwnerName(), $this->getOwnerKey());
    }
}
