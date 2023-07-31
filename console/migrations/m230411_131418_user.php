<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;
use common\base\enum\Gender;
use common\base\enum\LanguageCode;
use common\base\enum\UserStatus;
use common\base\enum\UserType;

/**
 * Class m230411_131418_user
 */
class m230411_131418_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createUserTable();
        $this->createUserEmailTable();
        $this->createUserPhoneTable();
        $this->createUserIdentityTable();
        $this->createUserSocialTable();

        $this->addForeignKey('email_fk', DatabaseTable::USER, 'emailId', DatabaseTable::USER_EMAIL, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('phone_fk', DatabaseTable::USER, 'phoneId', DatabaseTable::USER_PHONE, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('identity_fk', DatabaseTable::USER, 'identityId', DatabaseTable::USER_IDENTITY, 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::USER_SOCIAL);
        $this->dropForeignKey('user_fk', DatabaseTable::USER_SOCIAL);
        $this->dropTable(DatabaseTable::USER_SOCIAL);

        $this->dropUpdateTimestampTrigger(DatabaseTable::USER_PHONE);
        $this->dropForeignKey('user_fk', DatabaseTable::USER_PHONE);
        $this->dropForeignKey('phone_fk', DatabaseTable::USER);
        $this->dropTable(DatabaseTable::USER_PHONE);

        $this->dropUpdateTimestampTrigger(DatabaseTable::USER_EMAIL);
        $this->dropForeignKey('user_fk', DatabaseTable::USER_EMAIL);
        $this->dropForeignKey('email_fk', DatabaseTable::USER);
        $this->dropTable(DatabaseTable::USER_EMAIL);

        $this->dropUpdateTimestampTrigger(DatabaseTable::USER_IDENTITY);
        $this->dropForeignKey('user_fk', DatabaseTable::USER_IDENTITY);
        $this->dropForeignKey('image_fk', DatabaseTable::USER_IDENTITY);
        $this->dropForeignKey('identity_fk', DatabaseTable::USER);
        $this->dropTable(DatabaseTable::USER_IDENTITY);

        $this->dropUpdateTimestampTrigger(DatabaseTable::USER);
        $this->dropForeignKey('image_fk', DatabaseTable::USER);
        $this->dropForeignKey('bank_account_fk', DatabaseTable::USER);
        $this->dropForeignKey('address_fk', DatabaseTable::USER);
        $this->dropForeignKey('contact_fk', DatabaseTable::USER);
        $this->dropTable(DatabaseTable::USER);
    }

    /**
     * user table
     * @throws \yii\base\Exception
     */
    protected function createUserTable()
    {
        $this->createTable(DatabaseTable::USER, [
            'id' => $this->uuidPrimaryKey(),
            'roles' => $this->textArray()->null(),
            'type' => $this->string(64)->notNull()->defaultValue(UserType::CUSTOMER),
            'status' => $this->string(64)->notNull()->defaultValue(UserStatus::ACTIVE),
            'name' => $this->string(254)->null(),
            'username' => $this->string(128)->notNull()->unique(),
            'displayName' => $this->string(254)->null(),
            'emailId' => $this->uuid()->null()->comment('default email'),
            'phoneId' => $this->uuid()->null()->comment('default phone'),
            'identityId' => $this->uuid()->null()->comment('default identity'),
            'addressId' => $this->uuid()->null()->comment('default address'),
            'contactId' => $this->uuid()->null()->comment('default contact e.gg emergency'),
            'bankAccountId' => $this->uuid()->null()->comment('default bank account'),
            'imageId' => $this->uuid()->null()->comment('avatar image'),
            'firstName' => $this->string(254)->null(),
            'middleName' => $this->string(254)->null(),
            'lastName' => $this->string(254)->null(),
            'fullName' => $this->string(254)->null(),
            'nameFormat' => $this->string(254)->defaultValue('{firstName} {lastName}'),
            'description' => $this->text()->null(),
            'gender' => $this->string(32)->notNull()->defaultValue(Gender::OTHER), //Other or Decline to State
            'dateOfBirth' => $this->date()->null(),
            'currentOccupationId' => $this->uuid()->null(),
            'preferCategoryIds' => $this->uuidArray()->null()->comment('preferred category ids (enum)'),
            'transportations' => $this->textArray()->null()->comment('transportations available'),
            'skills' => $this->textArray()->null(),
            'membershipCode' => $this->string(64)->notNull()->defaultValue(''),
            'passwordSalt' => $this->string(192)->notNull(),
            'passwordHash' => $this->string(256)->notNull(),
            'passwordResetToken' => $this->string(128)->null()->unique(),
            'token' => $this->string(192)->notNull(),
            'authKey' => $this->string(64)->notNull(),
            'authMfaToken' => $this->string(128)->null(),
            'authCookieExpiry' => $this->integer()->notNull()->defaultValue(3600),
            'referrerCode' => $this->string(32)->notNull()->unique(),
            'referrerUserId' => $this->uuid(),
            'languageCode' => $this->string(8)->notNull()->defaultValue(LanguageCode::ENGLISH),
            'currencyCode' => $this->string(8)->notNull(),
            'countryCode' => $this->string(8)->null(),
            'stateCode' => $this->string(25)->null(),
            'cityId' => $this->uuid()->null(),
            'postcode' => $this->string(16)->null(),
            'lastActivityPk' => $this->uuid()->null(),
            'lastActivityData' => $this->json()->null(),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'cacheDeviceIdentifier' => $this->string(192)->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('user_authKey_idx', DatabaseTable::USER, 'authKey');
        $this->addForeignKey('bank_account_fk', DatabaseTable::USER, 'bankAccountId', DatabaseTable::BANK_ACCOUNT, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('address_fk', DatabaseTable::USER, 'addressId', DatabaseTable::ADDRESS, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('contact_fk', DatabaseTable::USER, 'contactId', DatabaseTable::CONTACT, 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('image_fk', DatabaseTable::USER, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::USER);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createUserEmailTable()
    {
        $this->createTable(DatabaseTable::USER_EMAIL, [
            'id' => $this->uuidPrimaryKey(),
            'userId' => $this->uuid()->notNull(),
            'address' => $this->string(254)->notNull()->unique(),
            'remark' => $this->text()->null(),
            'isVerified' => $this->boolean()->notNull()->defaultValue(false),
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

        $this->createIndex('user_email_user_idx', DatabaseTable::USER_EMAIL, 'userId');
        $this->addForeignKey('user_fk', DatabaseTable::USER_EMAIL, 'userId', DatabaseTable::USER, 'id', 'RESTRICT', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::USER_EMAIL);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createUserPhoneTable()
    {
        $this->createTable(DatabaseTable::USER_PHONE, [
            'id' => $this->uuidPrimaryKey(),
            'userId' => $this->uuid()->notNull(),
            'prefix' => $this->string(12)->notNull(),
            'number' => $this->string(32)->notNull(),
            'complete' => $this->string(64)->notNull()->unique(),
            'remark' => $this->text()->null(),
            'isVerified' => $this->boolean()->notNull()->defaultValue(false),
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

        $this->createIndex('user_phone_user_idx', DatabaseTable::USER_PHONE, 'userId');
        $this->createIndex('user_phone_idx', DatabaseTable::USER_PHONE, ['prefix', 'number'], true);
        $this->addForeignKey('user_fk', DatabaseTable::USER_PHONE, 'userId', DatabaseTable::USER, 'id', 'RESTRICT', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::USER_PHONE);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createUserIdentityTable()
    {
        $this->createTable(DatabaseTable::USER_IDENTITY, [
            'id' => $this->uuidPrimaryKey(),
            'userId' => $this->uuid()->notNull(),
            'status' => $this->string(64)->notNull()->comment('Pending, Verified, Rejected'),
            'countryCode' => $this->string(8)->notNull(),
            'type' => $this->string(128)->notNull()->comment('e.g. NRIC, Passport, etc, rejected will append random string with pipe e.g. NRIC|Sxjsd7x'),
            'name' => $this->string(256)->notNull()->comment('The identity name'),
            'number' => $this->string(128)->notNull()->comment('The identity number'),
            'key' => $this->string(128)->notNull()->comment('Pending one, is random string, verified one, set to "VERIFIED"'),
            'remark' => $this->text()->null(),
            'imageId' => $this->uuid()->null()->comment('Default Image'),
            'isVerified' => $this->boolean()->notNull()->defaultValue(false),
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

        $this->createIndex('user_identity_user_idx', DatabaseTable::USER_IDENTITY, 'userId');
        $this->createIndex('user_identity_status_idx', DatabaseTable::USER_IDENTITY, 'status');
        $this->createIndex('user_identity_value_idx', DatabaseTable::USER_IDENTITY, ['countryCode', 'type', 'number', 'key'], true);
        $this->createIndex('user_identity_owner_idx', DatabaseTable::USER_IDENTITY, ['userId', 'countryCode', 'type'], true);
        $this->addForeignKey('user_fk', DatabaseTable::USER_IDENTITY, 'userId', DatabaseTable::USER, 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('image_fk', DatabaseTable::USER_IDENTITY, 'imageId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::USER_IDENTITY);
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    protected function createUserSocialTable()
    {
        $this->createTable(DatabaseTable::USER_SOCIAL, [
            'id' => $this->uuidPrimaryKey(),
            'userId' => $this->uuid()->notNull(),
            'channel' => $this->string(32)->notNull()->comment('e.g. Facebook, Google, WeChat etc'),
            'channelId' => $this->string(256)->notNull()->comment('The actual id of the channel'),
            'channelName' => $this->string(256)->null()->comment('Name from the channel'),
            'channelAvatarImageSrc' => $this->text()->null()->comment('Avatar Image Src from the channel'),
            'isVerified' => $this->boolean()->notNull()->defaultValue(false),
            'token' => $this->string(192)->notNull()->unique(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->createIndex('user_social_user_idx', DatabaseTable::USER_SOCIAL, 'userId');
        $this->createIndex('user_social_idx', DatabaseTable::USER_SOCIAL, ['userId', 'channel'], true);
        $this->createIndex('user_social_channel_Idx', DatabaseTable::USER_SOCIAL, ['channel', 'channelId'], true);
        $this->addForeignKey('user_fk', DatabaseTable::USER_SOCIAL, 'userId', DatabaseTable::USER, 'id', 'RESTRICT', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::USER_SOCIAL);
    }
}
