<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;

/**
 * Class m230411_131415_address
 */
class m230411_131415_address extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::ADDRESS, [
            'id' => $this->uuidPrimaryKey(),
            'ownerType' => $this->string(128)->notNull()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->notNull()->comment('The string representation of pk or id'),
            'receiverName' => $this->string(192)->null(),
            'companyName' => $this->string(192)->null(),
            'phoneNumber' => $this->string(128)->null(),
            'address' => $this->text()->null(),
            'postcode' => $this->integer(16)->null(),
            'cityId' => $this->uuid()->null(),
            'stateCode' => $this->string(25)->null(),
            'countryCode' => $this->string(8)->null(),
            'latitude' => $this->double()->null(),
            'longitude' => $this->double()->null(),
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

        $this->createIndex('address_owner_idx', DatabaseTable::ADDRESS, ['ownerType', 'ownerKey']);
        $this->addForeignKey('city_fk', DatabaseTable::ADDRESS, 'cityId', DatabaseTable::CITY, 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('state_fk', DatabaseTable::ADDRESS, 'stateCode', DatabaseTable::STATE, 'code', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('country_fk', DatabaseTable::ADDRESS, 'countryCode', DatabaseTable::COUNTRY, 'code', 'RESTRICT', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::ADDRESS);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::ADDRESS);

        $this->dropIndex('address_owner_idx', DatabaseTable::ADDRESS);
        $this->dropForeignKey('city_fk', DatabaseTable::ADDRESS);
        $this->dropForeignKey('state_fk', DatabaseTable::ADDRESS);
        $this->dropForeignKey('country_fk', DatabaseTable::ADDRESS);

        $this->dropTable(DatabaseTable::ADDRESS);
    }
}
