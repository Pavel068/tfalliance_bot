<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%topic_replies}}`.
 */
class m220319_155757_create_topic_replies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%topic_replies}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'topic_id' => $this->integer()->notNull(),
            'bot_message_id' => $this->bigInteger()->notNull(),
            'message' => $this->text()->null()
        ]);

        $this->createIndex('topic_replies_user_id', 'topic_replies', 'user_id');
        $this->createIndex('topic_replies_topic_id', 'topic_replies', 'topic_id');

        $this->addForeignKey('fk_topic_replies_user_id', 'topic_replies', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_topic_replies_topic_id', 'topic_replies', 'topic_id', 'topics', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%topic_replies}}');
    }
}
