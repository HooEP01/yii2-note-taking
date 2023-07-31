<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131411_wallet
 */
class m230411_131411_wallet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createWalletTable();
        $this->createWalletTransactionTable();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::WALLET_TRANSACTION);
        $this->dropUpdateTimestampTrigger(DatabaseTable::WALLET);

        $this->dropForeignKey('wallet_fk', DatabaseTable::WALLET_TRANSACTION);
        $this->dropForeignKey('settlement_fk', DatabaseTable::WALLET_TRANSACTION);

        $this->dropTable(DatabaseTable::WALLET_TRANSACTION);
        $this->dropTable(DatabaseTable::WALLET);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createWalletTable()
    {
        $this->createTable(DatabaseTable::WALLET, [
            'id' => $this->uuidPrimaryKey(),
            'ownerType' => $this->string(128)->notNull()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->notNull()->comment('The string representation of pk or id'),
            'currencyCode' => $this->string(8)->notNull(),
            'magnifier' => $this->integer()->defaultValue(100)->notNull(),
            'precision' => $this->smallInteger()->notNull()->defaultValue(2),
            'cacheBalance' => $this->bigInteger()->notNull()->defaultValue(0)->comment('depend the decimal_precision, if 2, then 100 = 10000'),
            'cacheWithdrawable' => $this->bigInteger()->notNull()->defaultValue(0)->comment('depend the decimal_precision, if 2, then 100 = 10000'),
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

        $this->createIndex('owner_currency_idx', DatabaseTable::WALLET, ['ownerType', 'ownerKey', 'currencyCode'], true);
        $this->createUpdateTimestampTrigger(DatabaseTable::WALLET);
    }

    /**
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\Exception
     */
    protected function createWalletTransactionTable()
    {
        $this->createTable(DatabaseTable::WALLET_TRANSACTION, [
            'id' => $this->uuidPrimaryKey(),
            'walletId' => $this->uuid()->notNull(),
            'type' => $this->string(64)->notNull(),
            'description' => $this->text()->notNull()->comment('the english message'),
            'amount' => $this->bigInteger()->notNull()->comment('depend the decimal_precision, if 2, then 100 = 10000'),
            'magnifier' => $this->integer()->defaultValue(100)->notNull(),
            'precision' => $this->smallInteger()->notNull()->defaultValue(2),
            'referenceCode' => $this->string(128)->null()->comment('The reference code, e.g. model'),
            'referenceType' => $this->string(128)->null()->comment('e.g. table name'),
            'referenceKey' => $this->string(192)->null()->comment('e.g. the table pk or id'),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null()->comment('for storing any extra data required'),
            'translateCategory' => $this->string(64)->null(),
            'translateMessage' => $this->text()->null(),
            'translateData' => $this->json()->null(),
            'settlementId' => $this->uuid()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->addForeignKey('wallet_fk', DatabaseTable::WALLET_TRANSACTION, 'walletId', DatabaseTable::WALLET, 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('settlement_fk', DatabaseTable::WALLET_TRANSACTION, 'settlementId', DatabaseTable::SETTLEMENT, 'id', 'RESTRICT', 'CASCADE');
        $this->createIndex('wallet_transaction_wallet_id_idx', DatabaseTable::WALLET_TRANSACTION, ['walletId']);
        $this->createIndex('wallet_transaction_settlement_id_idx', DatabaseTable::WALLET_TRANSACTION, ['settlementId']);
        $this->createIndex('wallet_transaction_reference_idx', DatabaseTable::WALLET_TRANSACTION, ['referenceType', 'referenceKey']);
        $this->createIndex('wallet_transaction_referenceType_idx', DatabaseTable::WALLET_TRANSACTION, 'type');
        $this->createIndex('wallet_transaction_referenceCode_idx', DatabaseTable::WALLET_TRANSACTION, 'referenceCode');
        $this->createUpdateTimestampTrigger(DatabaseTable::WALLET_TRANSACTION);
    }
}
