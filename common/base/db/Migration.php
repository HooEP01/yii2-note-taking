<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\db;

use common\base\db\pgsql\ColumnSchemaBuilder;
use common\base\db\pgsql\Schema;
use yii\base\NotSupportedException;

/**
 * Class Migration
 * @package common\base\db
 */
class Migration extends \yii\db\Migration
{
    /**
     * make sure is MySQL 5.7, due to using st_distance_sphere function
     * @throws NotSupportedException
     */
    public function init()
    {
        parent::init();

        if ($this->db->driverName !== 'pgsql') {
            throw new NotSupportedException('This application only support Postgres v14.x');
        }

        $version = $this->db->serverVersion;
        if (strpos($version, '14.') === false) {
            throw new NotSupportedException('This application only support Postgres v14.x');
        }
    }

    /**
     * Creates a uuid column.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @throws NotSupportedException
     * @since 2.0.6
     */
    public function uuid(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_UUID);
    }

    /**
     * Creates a uuid column.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @throws NotSupportedException
     * @since 2.0.6
     */
    public function tsvector(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TSVECTOR);
    }

    /**
     * Creates a uuid column.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @throws NotSupportedException
     * @since 2.0.6
     */
    public function nanoid(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_NANOID);
    }

    /**
     * for uuid columns primary Key
     * ** uuid-ossp extension must be enabled
     */
    public function uuidPrimaryKey(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_UUIDPK);
    }

    /**
     * Creates a uuid column.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @throws NotSupportedException
     * @since 2.0.6
     */
    public function nanoidPrimaryKey(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_NANOID_PK);
    }

    /**
     * @return ColumnSchemaBuilder
     * @throws NotSupportedException
     */
    public function datetimeZone(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TIMESTAMP_TIMEZONE);
    }

    /**
     * @return ColumnSchemaBuilder
     */
    public function textArray(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT_ARRAY);
    }

    /**
     * @return ColumnSchemaBuilder
     */
    public function uuidArray(): ColumnSchemaBuilder
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_UUID_ARRAY);
    }

    /**
     * @param bool $always always generated will not allow provide value when insert
     * @return string
     */
    protected function bigPrimaryIdentity($always = false): string
    {
        if ($always) {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_BIG_IDENTITY_ALWAYS);
        }
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_BIG_IDENTITY_DEFAULT);
    }

    /**
     * @return string|null
     */
    protected function getTableOptions(): ?string
    {
        return null;
    }

    /**
     * create auto timestamp function
     */
    protected function createUpdateTimestampFunction()
    {
        $sql =<<<SQL
CREATE OR REPLACE FUNCTION set_updated_timestamp()
RETURNS TRIGGER AS $$
BEGIN
  NEW."updatedAt" = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
SQL;
        return $this->execute($sql);
    }

    /**
     * drop auto timestamp function
     */
    protected function dropUpdateTimestampFunction()
    {
        $sql = "DROP FUNCTION IF EXISTS set_updated_timestamp";
        return $this->execute($sql);
    }

    /**
     * @param $tableName
     */
    protected function createUpdateTimestampTrigger($tableName)
    {
        $sql = "CREATE TRIGGER auto_update_timestamp BEFORE UPDATE ON {$tableName} FOR EACH ROW EXECUTE PROCEDURE set_updated_timestamp()";
        return $this->execute($sql);
    }

    /**
     * @param $tableName
     */
    protected function dropUpdateTimestampTrigger($tableName)
    {
        return $this->dropTrigger('auto_update_timestamp', $tableName);
    }

    /**
     * @param $triggerName
     * @param $tableName
     */
    protected function dropTrigger($triggerName, $tableName)
    {
        $sql = "DROP TRIGGER IF EXISTS {$triggerName} ON {$tableName}";
        return $this->execute($sql);
    }
}
