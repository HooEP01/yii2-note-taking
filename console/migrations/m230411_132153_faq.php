<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_132153_faq
 */
class m230411_132153_faq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::FAQ, [
            'id' => $this->uuidPrimaryKey(),
            'type' => $this->string(32)->notNull(),
            'question' => $this->text()->null(),
            'htmlAnswer' => $this->text()->null(),
            'purifiedAnswer' => $this->text()->null(),
            'categoryIds' => $this->uuidArray()->null(),
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
        $this->createUpdateTimestampTrigger(DatabaseTable::FAQ);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::FAQ);
        $this->dropTable(DatabaseTable::FAQ);
    }
}
