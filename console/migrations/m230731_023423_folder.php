<?php

use common\base\db\Migration;

/**
 * Class m230731_023423_folder
 */
class m230731_023423_folder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%folder}}', [
            'id' => $this->uuidPrimaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'status' => $this->string(64)->null(),
            'createdBy' => $this->uuid()->notNull(),
            'createdAt' =>  $this->datetimeZone()->notNull()->defaultNow(),
            'updatedBy' => $this->uuid()->notNull(),
            'updatedAt' => $this->datetimeZone()->notNull()->defaultNow(),
            'deletedBy' => $this->uuid()->null(),
            'deletedAt' => $this->datetimeZone()->null(),
            'isActive' => $this->boolean()->notNull()->defaultValue(true),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230731_023423_folder cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230731_023423_folder cannot be reverted.\n";

        return false;
    }
    */
}
