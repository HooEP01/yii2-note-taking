<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131358_language
 */
class m230411_131358_language extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::LANGUAGE, [
            'code' => $this->string(8)->notNull(),
            'name' => $this->string(254)->notNull(),
            'shortName' => $this->string(128)->null(),
            'imageId' => $this->uuid()->null(),
            'position' => $this->integer()->defaultValue(999),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->addPrimaryKey('language_pk', DatabaseTable::LANGUAGE, 'code');
        $this->addForeignKey('image_fk', DatabaseTable::LANGUAGE, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::LANGUAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::LANGUAGE);
        $this->dropForeignKey('image_fk', DatabaseTable::LANGUAGE);
        $this->dropTable(DatabaseTable::LANGUAGE);
    }
}
