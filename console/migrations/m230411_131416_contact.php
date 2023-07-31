<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131416_contact
 */
class m230411_131416_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::CONTACT, [
            'id' => $this->uuidPrimaryKey(),
            'type' => $this->string(64)->notNull(),
            'ownerType' => $this->string(128)->notNull()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->notNull()->comment('The string representation of pk or id'),
            'displayName' => $this->text()->null(),
            'emailAddress' => $this->text()->null(),
            'phoneNumber' => $this->text()->null(),
            'relationshipId' => $this->uuid()->null()->comment('The relationship of the contact to the owner'),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' =>  $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('contact_owner_idx', DatabaseTable::CONTACT, ['ownerType', 'ownerKey']);
        $this->createIndex('contact_display_name_idx', DatabaseTable::CONTACT, ['displayName']);
        $this->createUpdateTimestampTrigger(DatabaseTable::CONTACT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::CONTACT);

        $this->dropIndex('contact_owner_idx', DatabaseTable::CONTACT);
        $this->dropIndex('contact_display_name_idx', DatabaseTable::CONTACT);

        $this->dropTable(DatabaseTable::CONTACT);
    }
}
