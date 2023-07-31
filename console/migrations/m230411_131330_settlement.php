<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131330_settlement
 */
class m230411_131330_settlement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::SETTLEMENT, [
            'id' => $this->uuidPrimaryKey(),
            'ownerType' => $this->string(128)->notNull()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->notNull()->comment('The string representation of pk or id'),
            'referenceNumber' => $this->string(64)->notNull()->unique(),
            'status' => $this->string(32)->null(),
            'currencyCode' => $this->string(32)->null(),
            'totalAmount' => $this->bigInteger()->null(),
            'magnifier' => $this->integer()->defaultValue(100)->notNull(),
            'precision' => $this->smallInteger()->notNull()->defaultValue(2),
            'transferAt' => $this->datetimeZone()->null(),
            'transferBankCode' => $this->string(16)->null(),
            'transferAccountName' => $this->text()->null(),
            'transferAccountNumber' => $this->text()->null(),
            'transferReference' => $this->text()->null(),
            'remark' => $this->text()->null(),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'token' => $this->string(192)->notNull()->unique(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('settlement_owner_idx', DatabaseTable::SETTLEMENT, ['ownerType', 'ownerKey']);
        $this->createIndex('settlement_status_idx', DatabaseTable::SETTLEMENT, ['status']);
        $this->createUpdateTimestampTrigger(DatabaseTable::SETTLEMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::SETTLEMENT);
        $this->dropTable(DatabaseTable::SETTLEMENT);
    }
}
