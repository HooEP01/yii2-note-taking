<?php

use common\base\db\Migration;


/**
 * Class m230731_023416_note
 */
class m230731_023416_note extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%note}}', [
            'id' => $this->uuidPrimaryKey(),
            'folder_id' => $this->uuid()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'tags' => $this->textArray()->null()->comment('from enum'),
            'priority' => $this->string(64)->null(),
            'due_date' => $this->dateTime()->null(),
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
        echo "m230731_023416_note cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230731_023416_note cannot be reverted.\n";

        return false;
    }
    */
}
