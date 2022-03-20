<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bot_actions}}`.
 */
class m220319_143555_create_bot_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bot_actions}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull(),
            'action' => $this->string()->null(),
            'created_at' => "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP",
            'updated_at' => "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_actions}}');
    }
}
