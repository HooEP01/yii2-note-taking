<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;
use common\base\enum\LanguageCode;
use yii\base\Exception;
use yii\base\NotSupportedException;

/**
 * Class m230411_125540_init
 */
class m230411_125540_init extends Migration
{
    /**
     * @return void
     * @throws Exception
     * @throws NotSupportedException
     */
    public function safeUp()
    {
        $this->enableExtensions();
        $this->createUpdateTimestampFunction();

        $this->createSessionTable();
        $this->createQueueTable();
        $this->createConfigTable();
        $this->createModelTranslationTable();
        $this->createModelConfigTable();
        $this->createModelCacheTable();

        $this->createUpdateTimestampTrigger(DatabaseTable::SYSTEM_CONFIG);
        $this->createUpdateTimestampTrigger(DatabaseTable::MODEL_CACHE);
        $this->createUpdateTimestampTrigger(DatabaseTable::MODEL_CONFIG);
        $this->createUpdateTimestampTrigger(DatabaseTable::MODEL_TRANSLATION);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::SYSTEM_CONFIG);
        $this->dropUpdateTimestampTrigger(DatabaseTable::MODEL_CACHE);
        $this->dropUpdateTimestampTrigger(DatabaseTable::MODEL_CONFIG);
        $this->dropUpdateTimestampTrigger(DatabaseTable::MODEL_TRANSLATION);

        $this->dropTable(DatabaseTable::MODEL_CACHE);
        $this->dropTable(DatabaseTable::MODEL_CONFIG);
        $this->dropTable(DatabaseTable::MODEL_TRANSLATION);
        $this->dropTable(DatabaseTable::HTTP_SESSION);
        $this->dropTable(DatabaseTable::SYSTEM_QUEUE);
        $this->dropTable(DatabaseTable::SYSTEM_CONFIG);

        $this->dropUpdateTimestampFunction();
        $this->disableExtensions();
    }

    /**
     * enabled some pgsql extension
     * - cube and earthdistance for calculate distance
     */
    protected function enableExtensions()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS "cube";');
        $this->execute('CREATE EXTENSION IF NOT EXISTS "earthdistance";');
    }

    /**
     * disabled extension for down function
     */
    protected function disableExtensions()
    {
        $this->execute('DROP EXTENSION IF EXISTS "earthdistance";');
        $this->execute('DROP EXTENSION IF EXISTS "cube";');
    }

    /**
     * @throws NotSupportedException
     */
    protected function createConfigTable()
    {
        $this->createTable(DatabaseTable::SYSTEM_CONFIG, [
            'id' => $this->uuidPrimaryKey(),
            'type' => $this->string(64)->notNull(),
            'name' => $this->string(128)->unique()->notNull(),
            'description' => $this->text()->null(),
            'content' => $this->text()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());
    }

    /**
     * @throws NotSupportedException
     * @throws Exception
     */
    protected function createSessionTable()
    {
        $this->createTable(DatabaseTable::HTTP_SESSION, [
            'id' => $this->string()->notNull(),
            'expire' => $this->integer(),
            'data' => $this->binary()->null(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'PRIMARY KEY ([[id]])',
        ], $this->getTableOptions());
    }

    /**
     * @return void
     */
    protected function createQueueTable()
    {
        $this->createTable(DatabaseTable::SYSTEM_QUEUE, [
            'id' => $this->uuidPrimaryKey(),
            'code' => $this->string(128)->notNull()->unique(),
            'channel' => $this->string(255)->notNull(),
            'job' => $this->binary()->notNull(),
            'pushedAt' => $this->integer()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->notNull()->defaultValue(0),
            'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
            'reservedAt' => $this->integer()->null(),
            'attempt' => $this->integer()->null(),
            'doneAt' => $this->integer()->null(),
        ], $this->getTableOptions());
        $this->createIndex('system_queue_channel', DatabaseTable::SYSTEM_QUEUE, 'channel');
        $this->createIndex('system_queue_reserved_at', DatabaseTable::SYSTEM_QUEUE, 'reservedAt');
        $this->createIndex('system_queue_priority', DatabaseTable::SYSTEM_QUEUE, 'priority');
    }

    /**
     * @throws NotSupportedException
     */
    protected function createModelTranslationTable()
    {
        $this->createTable(DatabaseTable::MODEL_TRANSLATION, [
            'id' => $this->uuidPrimaryKey(),
            'language' => $this->string(8)->notNull()->defaultValue(LanguageCode::ENGLISH),
            'code' => $this->string(128)->notNull(),
            'ownerType' => $this->string(128)->null()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->null()->comment('The string representation of pk or id'),
            'ownerAttribute' => $this->string(128)->null()->comment('The attribute name'),
            'message' => $this->string()->notNull(),
            'content' => $this->text()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('model_translation_owner_idx', DatabaseTable::MODEL_TRANSLATION, ['ownerType', 'ownerKey', 'ownerAttribute']);
        $this->createIndex('model_translation_language_msg_idx', DatabaseTable::MODEL_TRANSLATION, ['language', 'message'], true);
        $this->createIndex('model_translation_code_idx', DatabaseTable::MODEL_TRANSLATION, 'code');
        $this->createIndex('model_translation_message_idx', DatabaseTable::MODEL_TRANSLATION, 'message');
    }

    /**
     * @throws NotSupportedException
     */
    protected function createModelConfigTable()
    {
        $this->createTable(DatabaseTable::MODEL_CONFIG, [
            'id' => $this->uuidPrimaryKey(),
            'ownerType' => $this->string(128)->null(),
            'ownerKey' => $this->string(192)->null(),
            'name' => $this->string(128)->notNull(),
            'type' => $this->string(64)->notNull(),
            'remark' => $this->text()->null(),
            'content' => $this->text()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());
        $this->createIndex('model_config_unique_idx', DatabaseTable::MODEL_CONFIG, ['ownerType', 'ownerKey', 'name'], true);
    }

    /**
     * @throws NotSupportedException
     */
    protected function createModelCacheTable()
    {
        $this->createTable(DatabaseTable::MODEL_CACHE, [
            'id' => $this->uuidPrimaryKey(),
            'code' => $this->string(128)->notNull(),
            'ownerType' => $this->string(128)->null()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->null()->comment('The string representation of pk or id'),
            'content' => $this->text()->null(),
            'expiryDatetime' => $this->datetimeZone()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());
        $this->createIndex('model_cache_unique_idx', DatabaseTable::MODEL_CACHE, ['ownerType', 'ownerKey', 'code'], true);
    }
}
