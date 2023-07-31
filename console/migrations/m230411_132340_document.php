<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_132340_document
 */
class m230411_132340_document extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::DOCUMENT, [
            'id' => $this->uuidPrimaryKey(),
            'code' => $this->string(128)->null(),
            'name' => $this->string(160)->notNull(),
            'ownerType' => $this->string(128)->null()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(128)->null()->comment('The string representation of pk or id'),
            'format' => $this->string(128)->null()->comment('e.g. image/png'),
            'extension' => $this->string(128)->null()->comment('e.g. pdf, jpg, docx'),
            'size' => $this->bigInteger()->null()->comment('In Bytes'),
            'src' => $this->text()->null(),
            'position' => $this->integer()->defaultValue(999)->comment('sorting, or ordering purpose'),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->text()->null()->comment('For storing, raw json special non-searchable info'),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' =>  $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('document_code_idx', DatabaseTable::DOCUMENT, 'code');
        $this->createIndex('document_owner_idx', DatabaseTable::DOCUMENT, ['ownerType', 'ownerKey']);
        $this->createUpdateTimestampTrigger(DatabaseTable::DOCUMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::DOCUMENT);
        $this->dropTable(DatabaseTable::DOCUMENT);
    }
}
