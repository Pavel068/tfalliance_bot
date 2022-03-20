<?php

use yii\db\Migration;

/**
 * Class m220320_071954_alter_topics_table
 */
class m220320_071954_alter_topics_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('topics', 'tg_link');
        $this->addColumn('topics', 'bot_message_id', $this->bigInteger()->notNull()->after('user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220320_071954_alter_topics_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220320_071954_alter_topics_table cannot be reverted.\n";

        return false;
    }
    */
}
