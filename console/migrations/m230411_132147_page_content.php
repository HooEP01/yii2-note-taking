<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_132147_page_content
 */
class m230411_132147_page_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::PAGE_CONTENT, [
            'id' => $this->uuidPrimaryKey(),
            'code' => $this->string(128)->notNull()->unique(),
            'slug' => $this->text()->null(),
            'name' => $this->string(128)->null(),
            'title' => $this->text()->null(),
            'htmlContent' => $this->text()->null(),
            'purifiedContent' => $this->text()->null(),
            'position' => $this->integer()->notNull()->defaultValue(999),
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

        $this->createUpdateTimestampTrigger(DatabaseTable::PAGE_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::PAGE_CONTENT);
        $this->dropTable(DatabaseTable::PAGE_CONTENT);
    }
}
