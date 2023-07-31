<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131340_currency_and_rate
 */
class m230411_131340_currency_and_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createCurrencyTable();
        $this->createCurrencyRateTable();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::CURRENCY_RATE);
        $this->dropForeignKey('source_fk', DatabaseTable::CURRENCY_RATE);
        $this->dropForeignKey('target_fk', DatabaseTable::CURRENCY_RATE);
        $this->dropTable(DatabaseTable::CURRENCY_RATE);

        $this->dropUpdateTimestampTrigger(DatabaseTable::CURRENCY);
        $this->dropForeignKey('image_fk', DatabaseTable::CURRENCY);
        $this->dropTable(DatabaseTable::CURRENCY);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createCurrencyTable()
    {
        $this->createTable(DatabaseTable::CURRENCY, [
            'code' => $this->string(8)->notNull(),
            'name' => $this->string(254)->notNull(),
            'shortName' => $this->string(128)->null(),
            'imageId' => $this->uuid()->null(),
            'symbol' => $this->string(8)->notNull()->defaultValue('$'),
            'format' => $this->string(64)->notNull()->defaultValue(\common\base\enum\CurrencyFormat::SYMBOL_VALUE),
            'magnifier' => $this->integer()->defaultValue(100)->notNull(),
            'precision' => $this->smallInteger()->notNull()->defaultValue(2),
            'decimalPoint' => $this->string(8)->notNull()->defaultValue('.'),
            'thousandsSeparator' => $this->string(8)->notNull()->defaultValue(','),
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

        $this->addPrimaryKey('currency_pk', DatabaseTable::CURRENCY, 'code');
        $this->addForeignKey('image_fk', DatabaseTable::CURRENCY, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::CURRENCY);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createCurrencyRateTable()
    {
        $this->createTable(DatabaseTable::CURRENCY_RATE, [
            'id' => $this->uuidPrimaryKey(),
            'sourceCurrencyCode' => $this->string(8)->notNull(),
            'targetCurrencyCode' => $this->string(8)->notNull(),
            'conversionRate' => $this->bigInteger()->notNull()->defaultValue(1000000)->comment('depend the precision, if 6, then 0.23 = 230000'),
            'magnifier' => $this->integer()->defaultValue(1000000)->notNull(),
            'precision' => $this->smallInteger()->notNull()->defaultValue(6),
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

        $this->addForeignKey('source_fk', DatabaseTable::CURRENCY_RATE, 'sourceCurrencyCode', DatabaseTable::CURRENCY, 'code', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('target_fk', DatabaseTable::CURRENCY_RATE, 'targetCurrencyCode', DatabaseTable::CURRENCY, 'code', 'RESTRICT', 'CASCADE');
        $this->createIndex('currency_rate_unique_idx', DatabaseTable::CURRENCY_RATE, ['sourceCurrencyCode', 'targetCurrencyCode'], true);
        $this->createUpdateTimestampTrigger(DatabaseTable::CURRENCY_RATE);
    }
}
