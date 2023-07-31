<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_132635_audit_trail
 */
class m230411_132635_audit_trail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::AUDIT_ENTRY, [
            'id' => $this->uuidPrimaryKey(),
            'userId' => $this->uuid()->null(),
            'ipAddress' => $this->string(45)->null(),
            'requestMethod' => $this->string(32)->null(),
            'requestRoute' => $this->string()->null(),
            'isAjax' => $this->boolean()->notNull()->defaultValue(false),
            'duration' => $this->float()->null(),
            'memoryMax' => $this->bigInteger()->null(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
        ], $this->getTableOptions());

        $this->createIndex('audit_entry_user_id_idx', DatabaseTable::AUDIT_ENTRY, ['userId']);
        $this->createIndex('audit_entry_request_route_idx', DatabaseTable::AUDIT_ENTRY, ['requestRoute']);

        $this->createTable(DatabaseTable::AUDIT_TRAIL, [
            'id' => $this->uuidPrimaryKey(),
            'entryId' => $this->uuid()->null(),
            'userId' => $this->uuid()->null(),
            'action' => $this->string(32)->null(),
            'ownerReference' => $this->string()->null(),
            'tableName' => $this->string()->null(),
            'modelClass' => $this->string()->null(),
            'modelKey' => $this->string()->null(),
            'field' => $this->string()->null(),
            'oldValue' => $this->text()->null(),
            'newValue' => $this->text()->null(),
            'traceAt' => $this->datetimeZone()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
        ], $this->getTableOptions());

        $this->addForeignKey('entry_pk', DatabaseTable::AUDIT_TRAIL, ['entryId'], DatabaseTable::AUDIT_ENTRY, 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('audit_trail_user_id_idx', DatabaseTable::AUDIT_TRAIL, ['userId']);
        $this->createIndex('audit_trail_owner_reference_idx', DatabaseTable::AUDIT_TRAIL, ['ownerReference']);
        $this->createIndex('audit_trail_search_table_idx', DatabaseTable::AUDIT_TRAIL, ['tableName', 'modelKey', 'field']);
        $this->createIndex('audit_trail_search_class_idx', DatabaseTable::AUDIT_TRAIL, ['modelClass', 'modelKey', 'field']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('entry_pk', DatabaseTable::AUDIT_TRAIL);
        $this->dropTable(DatabaseTable::AUDIT_TRAIL);
        $this->dropTable(DatabaseTable::AUDIT_ENTRY);
    }
}
