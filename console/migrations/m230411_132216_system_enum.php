<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_132216_system_enum
 */
class m230411_132216_system_enum extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::SYSTEM_ENUM, [
            'id' => $this->uuidPrimaryKey(),
            'type' => $this->string(128)->notNull(),
            'code' => $this->string(128)->notNull(),
            'name' => $this->string(192)->notNull(),
            'description' => $this->text()->null(),
            'remark' => $this->text()->null(),
            'imageId' => $this->uuid()->null(),
            'parentId' => $this->uuid()->null(),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'position' => $this->integer()->defaultValue(999)->comment('sorting, or ordering purpose'),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' =>  $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('system_enum_type_idx', DatabaseTable::SYSTEM_ENUM, ['type']);
        $this->createIndex('system_enum_parent_id_idx', DatabaseTable::SYSTEM_ENUM, ['parentId']);
        $this->createIndex('system_enum_unique_idx', DatabaseTable::SYSTEM_ENUM, ['type', 'code'], true);
        $this->createUpdateTimestampTrigger(DatabaseTable::SYSTEM_ENUM);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::SYSTEM_ENUM);
        $this->dropIndex('system_enum_type_idx', DatabaseTable::SYSTEM_ENUM);
        $this->dropIndex('system_enum_parent_id_idx', DatabaseTable::SYSTEM_ENUM);
        $this->dropIndex('system_enum_unique_idx', DatabaseTable::SYSTEM_ENUM);
        $this->dropTable(DatabaseTable::SYSTEM_ENUM);
    }
}
