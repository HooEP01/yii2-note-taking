<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131412_bank
 */
class m230411_131412_bank extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::BANK, [
            'code' => $this->string(16)->notNull()->comment('official first 6 characters of bank swift code. e.g. AMPBAU for AMP Bank'),
            'countryCode' => $this->string(8)->notNull()->comment('normally the last 2 chars of swift code'),
            'name' => $this->string()->notNull(),
            'shortName' => $this->string()->null(),
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

        $this->addPrimaryKey('bank_pk', DatabaseTable::BANK, 'code');
        $this->addForeignKey('image_fk', DatabaseTable::BANK, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::BANK);

        $this->createTable(DatabaseTable::BANK_ACCOUNT, [
            'id' => $this->uuidPrimaryKey(),
            'bankCode' => $this->string(16)->notNull(),
            'ownerType' => $this->string(128)->notNull()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->notNull()->comment('The string representation of pk or id'),
            'type' => $this->string(32)->notNull()->comment('business / personal'),
            'name' => $this->string(256)->null(),
            'number' => $this->string(128)->null(),
            'isVerified' => $this->boolean()->notNull()->defaultValue(false),
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

        $this->createIndex('bank_account_owner_idx', DatabaseTable::BANK_ACCOUNT, ['ownerType', 'ownerKey']);
        $this->addForeignKey('bank_fk', DatabaseTable::BANK_ACCOUNT, 'bankCode', DatabaseTable::BANK, 'code', 'RESTRICT', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::BANK_ACCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::BANK_ACCOUNT);
        $this->dropForeignKey('bank_fk', DatabaseTable::BANK_ACCOUNT);
        $this->dropTable(DatabaseTable::BANK_ACCOUNT);

        $this->dropUpdateTimestampTrigger(DatabaseTable::BANK);
        $this->dropForeignKey('image_fk', DatabaseTable::BANK);
        $this->dropTable(DatabaseTable::BANK);
    }
}
