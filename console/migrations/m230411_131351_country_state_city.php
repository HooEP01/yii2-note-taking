<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131351_country_state_city
 */
class m230411_131351_country_state_city extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createCountryTable();
        $this->createStateTable();
        $this->createCityTable();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::CITY);
        $this->dropForeignKey('country_fk', DatabaseTable::CITY);
        $this->dropForeignKey('state_fk', DatabaseTable::CITY);
        $this->dropForeignKey('image_fk', DatabaseTable::CITY);
        $this->dropTable(DatabaseTable::CITY);

        $this->dropUpdateTimestampTrigger(DatabaseTable::STATE);
        $this->dropForeignKey('country_fk', DatabaseTable::STATE);
        $this->dropForeignKey('image_fk', DatabaseTable::STATE);
        $this->dropTable(DatabaseTable::STATE);

        $this->dropUpdateTimestampTrigger(DatabaseTable::COUNTRY);
        $this->dropForeignKey('image_fk', DatabaseTable::COUNTRY);
        $this->dropTable(DatabaseTable::COUNTRY);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createCountryTable()
    {
        $this->createTable(DatabaseTable::COUNTRY, [
            'code' => $this->string(8)->notNull(),
            'name' => $this->string(254)->notNull(),
            'shortName' => $this->string(128)->notNull(),
            'imageId' => $this->uuid()->null(),
            'iso3' => $this->string(8)->null(),
            'numCode' => $this->string(8)->null(),
            'telCode' => $this->string(8)->null(),
            'currencyCode' => $this->string(8)->null(),
            'defaultStateCode' => $this->string(25)->null(),
            'isStateRequired' => $this->boolean()->defaultValue(true),
            'isPostcodeRequired' => $this->boolean()->defaultValue(true),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'position' => $this->integer()->defaultValue(999),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->addPrimaryKey('country_pk', DatabaseTable::COUNTRY, 'code');
        $this->addForeignKey('image_fk', DatabaseTable::COUNTRY, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::COUNTRY);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createStateTable()
    {
        $this->createTable(DatabaseTable::STATE, [
            'code' => $this->string(25)->notNull(),
            'name' => $this->string(254)->notNull(),
            'shortName' => $this->string(128)->notNull(),
            'imageId' => $this->uuid()->null(),
            'countryCode' => $this->string(8)->notNull(),
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

        $this->createIndex('state_country_idx', DatabaseTable::STATE, 'countryCode');
        $this->addPrimaryKey('state_pk', DatabaseTable::STATE, 'code');
        $this->addForeignKey('country_fk', DatabaseTable::STATE, 'countryCode', DatabaseTable::COUNTRY, 'code', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('image_fk', DatabaseTable::STATE, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::STATE);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createCityTable()
    {
        $this->createTable(DatabaseTable::CITY, [
            'id' => $this->uuidPrimaryKey(),
            'name' => $this->string(254)->notNull(),
            'shortName' => $this->string(128)->null(),
            'imageId' => $this->uuid()->null(),
            'stateCode' => $this->string(25)->notNull(),
            'countryCode' => $this->string(8)->notNull(),
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

        $this->createIndex('city_country_idx', DatabaseTable::CITY, 'countryCode');
        $this->createIndex('city_state_idx', DatabaseTable::CITY, 'stateCode');

        $this->addForeignKey('country_fk', DatabaseTable::CITY, 'countryCode', DatabaseTable::COUNTRY, 'code', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('state_fk', DatabaseTable::CITY, 'stateCode', DatabaseTable::STATE, 'code', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('image_fk', DatabaseTable::CITY, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::CITY);
    }
}
