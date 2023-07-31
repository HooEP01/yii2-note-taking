<?php

use common\base\db\Migration;
use common\base\enum\DatabaseTable;


/**
 * Class m230411_131300_image
 */
class m230411_131300_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DatabaseTable::IMAGE, [
            'id' => $this->uuidPrimaryKey(),
            'code' => $this->string(128)->null(),
            'name' => $this->string(160)->notNull(),
            'title' => $this->text()->null()->comment('For image title'),
            'caption' => $this->text()->null()->comment('For image alt'),
            'ownerType' => $this->string(128)->null()->comment('Table name of the model or class of owner'),
            'ownerKey' => $this->string(192)->null()->comment('The string representation of pk or id'),
            'format' => $this->string(128)->null()->comment('e.g. image/png'),
            'extension' => $this->string(128)->null()->comment('e.g. png, jpg, gif'),
            'size' => $this->bigInteger()->null()->comment('In Bytes'),
            'width' => $this->integer()->null()->comment('In Pixel'),
            'height' => $this->integer()->null()->comment('In Pixel'),
            'src' => $this->text()->null(),
            'callToAction' => $this->json()->null(),
            'configuration' => $this->json()->null(),
            'cacheTranslation' => $this->json()->null(),
            'cacheData' => $this->json()->null(),
            'cacheIndex' => $this->json()->null(),
            'position' => $this->integer()->defaultValue(999)->comment('sorting, or ordering purpose'),
            'variant' => $this->string(64)->comment('e.g. hover, mobile, etc'),
            'parentId' => $this->uuid()->null(),
            'accountId' => $this->uuid()->null(),
            'isPrivate' => $this->boolean()->notNull()->defaultValue(false),
            'isSystem' => $this->boolean()->notNull()->defaultValue(false),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());
        $this->addCommentOnColumn(DatabaseTable::IMAGE, 'parentId', 'for variant, to known which is parent image');

        $this->createIndex('image_code_idx', DatabaseTable::IMAGE, 'code');
        $this->createIndex('image_owner_idx', DatabaseTable::IMAGE, ['ownerType', 'ownerKey']);
        $this->addForeignKey('parent_fk', DatabaseTable::IMAGE, 'parentId', DatabaseTable::IMAGE, 'id', 'SET NULL', 'CASCADE');
        $this->createUpdateTimestampTrigger(DatabaseTable::IMAGE);

        $this->createTable(DatabaseTable::IMAGE_CACHE, [
            'id' => $this->uuidPrimaryKey(),
            'status' => $this->string(64)->notNull()->comment('e.g. Pending, Generated, Deleting, Deleted'),
            'imageId' => $this->uuid()->notNull(),
            'format' => $this->string(128)->null()->comment('e.g. image/png'),
            'extension' => $this->string(128)->null()->comment('e.g. png, jpg, gif'),
            'size' => $this->integer()->null()->comment('In Bytes'),
            'width' => $this->integer()->null()->comment('In Pixel'),
            'height' => $this->integer()->null()->comment('In Pixel'),
            'src' => $this->text()->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ], $this->getTableOptions());

        $this->addForeignKey('image_fk', DatabaseTable::IMAGE_CACHE, 'imageId', DatabaseTable::IMAGE, 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('image_cache_image_idx', DatabaseTable::IMAGE_CACHE, 'imageId');
        $this->createIndex('image_cache_status_idx', DatabaseTable::IMAGE_CACHE, 'status');
        $this->createIndex('image_cache_dimension_idx', DatabaseTable::IMAGE_CACHE, ['width', 'height']);
        $this->createIndex('image_cache_unique_idx', DatabaseTable::IMAGE_CACHE, ['imageId', 'width', 'height', 'format', 'extension'], true);
        $this->createUpdateTimestampTrigger(DatabaseTable::IMAGE_CACHE);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropUpdateTimestampTrigger(DatabaseTable::IMAGE);
        $this->dropUpdateTimestampTrigger(DatabaseTable::IMAGE_CACHE);

        $this->dropForeignKey('parent_fk', DatabaseTable::IMAGE);
        $this->dropForeignKey('image_fk', DatabaseTable::IMAGE_CACHE);

        $this->dropTable(DatabaseTable::IMAGE_CACHE);
        $this->dropTable(DatabaseTable::IMAGE);
    }
}
